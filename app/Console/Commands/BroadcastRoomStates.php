<?php

namespace App\Console\Commands;

use App\Events\RoomStateSynced;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class BroadcastRoomStates extends Command
{
    protected $signature = 'rooms:broadcast-states';

    protected $description = '廣播所有房間的狀態同步事件';

    public function handle()
    {
        try {
            // 直接使用 Laravel Redis facade 找出所有房間
            $allKeys = Redis::keys('*room:*');
            $rooms = [];
            
            foreach ($allKeys as $key) {

                if (preg_match('/room:([A-Z0-9]+)$/', $key, $matches)) {
                    $roomCode = $matches[1];

                    // 檢查房間是否仍然存在且有效 (使用 room:代碼 格式，讓 Laravel 自動處理前綴)
                    if (Redis::exists("room:{$roomCode}")) {
                        $rooms[] = $roomCode;
                        $this->info("房間 {$roomCode} 添加到列表");
                    } else {
                        $this->warn("房間 {$roomCode} 不存在或無效");
                    }
                }
            }

            $broadcastCount = 0;

            foreach ($rooms as $roomCode) {
                try {
                    // 獲取房間數據 (Laravel Redis facade 會自動添加前綴)
                    $roomData = Redis::hgetall("room:{$roomCode}");
                    $votes = Redis::hgetall("votes:{$roomCode}");
                    $participants = Redis::smembers("participants:{$roomCode}");
                    $inactiveParticipants = Redis::smembers("inactive:{$roomCode}");
                    $offlineParticipants = Redis::smembers("offline:{$roomCode}");
                    
                    // 總是廣播房間狀態，不管是否有參與者 (確保同步)
                    try {
                        broadcast(new RoomStateSynced($roomCode, $roomData, $votes, $participants, $inactiveParticipants, $offlineParticipants));
                        $broadcastCount++;
                        
                        if (!empty($participants)) {
                            $this->info("✅ 廣播房間狀態同步: {$roomCode} (參與者: " . count($participants) . ", 投票: " . count($votes) . ")");
                        } else {
                            $this->info("📡 廣播空房間狀態同步: {$roomCode} (確保同步)");
                        }
                    } catch (\Exception $broadcastError) {
                        $this->warn("❌ 廣播房間 {$roomCode} 失敗 (WebSocket 可能未連接): " . $broadcastError->getMessage());
                    }
                } catch (\Exception $e) {
                    $this->error("廣播房間 {$roomCode} 狀態時發生錯誤: " . $e->getMessage());
                }
            }

            if ($broadcastCount > 0) {
                $this->info("成功廣播 {$broadcastCount} 個房間的狀態同步事件");
            } else {
                // 顯示調試資訊
                $allKeys = Redis::keys('*room:*');
                $roomKeys = array_filter($allKeys, function($key) {
                    return strpos($key, 'room:') !== false;
                });
                
                $this->info("目前沒有活躍的房間需要廣播");
                $this->info("Redis 中的房間數量: " . count($roomKeys));
                if (count($roomKeys) > 0) {
                    $this->info("現有房間: " . implode(', ', $roomKeys));
                    
                    // 檢查每個房間的參與者
                    foreach ($roomKeys as $roomKey) {
                        if (preg_match('/room:([A-Z0-9]+)$/', $roomKey, $matches)) {
                            $roomCode = $matches[1];
                            $participants = Redis::smembers("participants:{$roomCode}");
                            $this->info("房間 {$roomCode} 參與者數量: " . count($participants));
                        }
                    }
                }
            }
            
        } catch (\Exception $e) {
            $this->error("Redis 連接錯誤: " . $e->getMessage());
            $this->info("請確認 Redis 服務是否運行且配置正確");
        }
        
        return Command::SUCCESS;
    }
}