<?php

namespace App\Console\Commands;

use App\Events\ParticipantInactive;
use App\Events\ParticipantReactivated;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CleanupInactiveUsers extends Command
{
    protected $signature = 'users:cleanup-inactive';
    protected $description = '清理超過30秒未活動的用戶';

    public function handle()
    {
        try {
            // 找出所有房間
            $allKeys = Redis::keys('*room:*');
            $rooms = [];
            
            foreach ($allKeys as $key) {
                if (preg_match('/room:([A-Z0-9]+)$/', $key, $matches)) {
                    $roomCode = $matches[1];
                    if (Redis::exists("room:{$roomCode}")) {
                        $rooms[] = $roomCode;
                    }
                }
            }

            $processedCount = 0;

            foreach ($rooms as $roomCode) {
                try {
                    // 獲取房間數據以識別主持人
                    $roomData = Redis::hgetall("room:{$roomCode}");
                    $hostName = $roomData['host'] ?? null;
                    
                    // 檢查活躍、非活躍和離線用戶的最後活動時間
                    $participants = Redis::smembers("participants:{$roomCode}");
                    $inactiveParticipants = Redis::smembers("inactive:{$roomCode}");
                    $offlineParticipants = Redis::smembers("offline:{$roomCode}");
                    $allUsers = array_merge($participants, $inactiveParticipants, $offlineParticipants);
                    
                    // 確保主持人不在參與者列表中
                    if ($hostName && in_array($hostName, $participants)) {
                        Redis::srem("participants:{$roomCode}", $hostName);
                        $this->info("🔧 移除主持人 {$hostName} 從參與者列表");
                    }
                    
                    // 確保主持人不在非活躍列表中
                    if ($hostName && in_array($hostName, $inactiveParticipants)) {
                        Redis::srem("inactive:{$roomCode}", $hostName);
                        $this->info("移除主持人 {$hostName} 從非活躍列表");
                    }
                    
                    // 確保主持人不在離線列表中
                    if ($hostName && in_array($hostName, $offlineParticipants)) {
                        Redis::srem("offline:{$roomCode}", $hostName);
                        $this->info("移除主持人 {$hostName} 從離線列表");
                    }
                    
                    $movedToInactive = 0;
                    $movedToOffline = 0;
                    $reactivated = 0;
                    
                    foreach ($allUsers as $user) {
                        // 跳過主持人
                        if ($user === $hostName) {
                            // 清理主持人的舊心跳 key
                            $oldHeartbeatKey = "heartbeat:{$roomCode}:{$user}";
                            if (Redis::exists($oldHeartbeatKey)) {
                                Redis::del($oldHeartbeatKey);
                            }
                            continue;
                        }
                        
                        $lastActivity = Redis::get("last_activity:{$roomCode}:{$user}");
                        $isActive = Redis::sismember("participants:{$roomCode}", $user);
                        $isInactive = Redis::sismember("inactive:{$roomCode}", $user);
                        $isOffline = Redis::sismember("offline:{$roomCode}", $user);
                        
                        // 清理舊的心跳 key（如果存在）
                        $oldHeartbeatKey = "heartbeat:{$roomCode}:{$user}";
                        if (Redis::exists($oldHeartbeatKey)) {
                            Redis::del($oldHeartbeatKey);
                        }
                        
                        if ($lastActivity) {
                            $timeSinceLastActivity = time() - $lastActivity;
                            
                            if ($timeSinceLastActivity <= 600) {
                                // 30秒內有活動 - 確保在 active 狀態
                                if ($isInactive || $isOffline) {
                                    // 從 inactive 或 offline 恢復到 active
                                    Redis::srem("inactive:{$roomCode}", $user);
                                    Redis::srem("offline:{$roomCode}", $user);
                                    Redis::sadd("participants:{$roomCode}", $user);
                                    
                                    $updatedParticipants = Redis::smembers("participants:{$roomCode}");
                                    $updatedInactive = Redis::smembers("inactive:{$roomCode}");
                                    
                                    broadcast(new ParticipantReactivated($roomCode, $user, $updatedParticipants, $updatedInactive));
                                    
                                    $this->info("用戶 {$user} 在房間 {$roomCode} 重新激活");
                                    $reactivated++;
                                }
                            } elseif ($timeSinceLastActivity <= 3600) {
                                // 30秒-10分鐘無活動 - 移動到 inactive
                                if ($isActive) {
                                    Redis::srem("participants:{$roomCode}", $user);
                                    Redis::sadd("inactive:{$roomCode}", $user);
                                    Redis::expire("inactive:{$roomCode}", 10800);
                                    
                                    $updatedParticipants = Redis::smembers("participants:{$roomCode}");
                                    $updatedInactive = Redis::smembers("inactive:{$roomCode}");
                                    
                                    broadcast(new ParticipantInactive($roomCode, $user, $updatedParticipants, $updatedInactive));
                                    
                                    $this->info("用戶 {$user} 在房間 {$roomCode} 暫時無回應，移至 inactive 狀態");
                                    $movedToInactive++;
                                }
                            } else {
                                // 超過10分鐘無活動 - 移動到 offline
                                if ($isActive || $isInactive) {
                                    Redis::srem("participants:{$roomCode}", $user);
                                    Redis::srem("inactive:{$roomCode}", $user);
                                    Redis::sadd("offline:{$roomCode}", $user);
                                    Redis::expire("offline:{$roomCode}", 10800);
                                    
                                    $updatedParticipants = Redis::smembers("participants:{$roomCode}");
                                    $updatedInactive = Redis::smembers("inactive:{$roomCode}");
                                    
                                    broadcast(new \App\Events\ParticipantOffline($roomCode, $user, $updatedParticipants, $updatedInactive));
                                    
                                    $this->info("用戶 {$user} 在房間 {$roomCode} 長時間無回應，移至 offline 狀態");
                                    $movedToOffline++;
                                }
                            }
                        } else if ($isActive) {
                            // 沒有活動記錄但在活躍列表中，設置當前時間
                            Redis::set("last_activity:{$roomCode}:{$user}", time());
                            Redis::expire("last_activity:{$roomCode}:{$user}", 3600);
                        }
                    }
                    
                    if ($movedToInactive > 0 || $movedToOffline > 0 || $reactivated > 0) {
                        $this->info("房間 {$roomCode}: {$movedToInactive} 個用戶變為 inactive, {$movedToOffline} 個用戶變為 offline, {$reactivated} 個用戶重新激活");
                    }
                    
                    $processedCount++;
                    
                } catch (\Exception $e) {
                    $this->error("處理房間 {$roomCode} 時發生錯誤: " . $e->getMessage());
                }
            }

            if ($processedCount > 0) {
                $this->info("已處理 {$processedCount} 個房間的用戶狀態");
            } else {
                $this->info("目前沒有房間需要處理");
            }
            
        } catch (\Exception $e) {
            $this->error("用戶清理錯誤: " . $e->getMessage());
        }
        
        return Command::SUCCESS;
    }
}