<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;

class PlanningPokerController extends Controller
{
    // 入口頁面
    public function index()
    {
        return Inertia::render('PlanningPoker/Index', [
            'predefinedNames' => $this->getPredefinedNames()
        ]);
    }

    // 加入房間 (參與者)
    public function joinRoom(Request $request)
    {
        $roomCode = $request->input('room_code');
        $userName = $request->input('user_name', '匿名用戶');
        
        // 檢查房間是否存在
        if (!Redis::exists("room:{$roomCode}")) {
            return back()->withErrors(['room_code' => '房間不存在']);
        }

        return redirect()->route('poker.room', ['code' => $roomCode, 'name' => $userName]);
    }

    // 加入房間 (主持人)
    public function joinHost(Request $request)
    {
        $roomCode = $request->input('room_code');
        
        // 檢查房間是否存在
        if (!Redis::exists("room:{$roomCode}")) {
            return back()->withErrors(['room_code' => '房間不存在']);
        }

        return redirect()->route('poker.host', ['code' => $roomCode]);
    }

    // 創建房間 (主持人)
    public function createRoom(Request $request)
    {
        $roomCode = strtoupper(substr(md5(time()), 0, 6));
        $hostName = $request->input('host_name', '主持人');

        // 創建房間
        $roomData = [
            'host' => $hostName,
            'status' => 'voting', // voting, revealed
            'created_at' => now()->timestamp
        ];
        
        Redis::hset("room:{$roomCode}", $roomData);
        Redis::expire("room:{$roomCode}", 10800); // 3 小時過期
        
        // 設定房間自動過期時間戳，確保房間能維持3小時
        Redis::set("room_expiry:{$roomCode}", time() + 10800);
        Redis::expire("room_expiry:{$roomCode}", 10800);

        return redirect()->route('poker.host', ['code' => $roomCode]);
    }

    // 參與者房間頁面
    public function room(Request $request, $code)
    {
        if (!Redis::exists("room:{$code}")) {
            return redirect()->route('poker.index')->withErrors(['error' => '房間不存在']);
        }

        $userName = $request->query('name');
        
        // 如果沒有暱稱參數，顯示暱稱輸入頁面
        if (!$userName) {
            return Inertia::render('PlanningPoker/JoinRoom', [
                'roomCode' => $code,
                'predefinedNames' => $this->getPredefinedNames()
            ]);
        }

        // 檢查用戶是否已經在參與者列表中
        $isNewParticipant = !Redis::sismember("participants:{$code}", $userName);
        
        // 將用戶添加到參與者列表
        Redis::sadd("participants:{$code}", $userName);
        Redis::expire("participants:{$code}", 10800);
        
        // 設定用戶最後活動時間
        Redis::set("last_activity:{$code}:{$userName}", time());
        Redis::expire("last_activity:{$code}:{$userName}", 10800); // 3 小時過期

        $roomData = Redis::hgetall("room:{$code}");
        $votes = Redis::hgetall("votes:{$code}");
        $participants = Redis::smembers("participants:{$code}");
        $inactiveParticipants = Redis::smembers("inactive:{$code}");
        $offlineParticipants = Redis::smembers("offline:{$code}");
        
        // 如果是新參與者，廣播加入事件
        if ($isNewParticipant) {
            \Log::info("廣播參與者加入事件: {$userName} 加入房間 {$code}");
            
            // 立即廣播事件
            try {
                $event = new \App\Events\ParticipantJoined($code, $userName, $participants);
                broadcast($event);
                \Log::info("參與者加入事件廣播成功");
            } catch (\Exception $e) {
                \Log::error("參與者加入事件廣播失敗: " . $e->getMessage());
            }
        } else {
            // 即使不是新參與者，也要確保心跳時間戳是最新的
            \Log::info("用戶重新進入房間，更新心跳時間戳: {$userName} 房間 {$code}");
        }
        
        return Inertia::render('PlanningPoker/Room', [
            'roomCode' => $code,
            'userName' => $userName,
            'roomData' => $roomData,
            'votes' => $votes,
            'participants' => $participants,
            'inactiveParticipants' => $inactiveParticipants,
            'offlineParticipants' => $offlineParticipants,
            'cards' => $this->getCards()
        ]);
    }

    // 主持人房間頁面
    public function host($code)
    {
        if (!Redis::exists("room:{$code}")) {
            return redirect()->route('poker.index')->withErrors(['error' => '房間不存在']);
        }

        $roomData = Redis::hgetall("room:{$code}");
        $votes = Redis::hgetall("votes:{$code}");
        $participants = Redis::smembers("participants:{$code}");
        $inactiveParticipants = Redis::smembers("inactive:{$code}");
        $offlineParticipants = Redis::smembers("offline:{$code}");
        
        return Inertia::render('PlanningPoker/Host', [
            'roomCode' => $code,
            'roomData' => $roomData,
            'votes' => $votes,
            'participants' => $participants,
            'inactiveParticipants' => $inactiveParticipants,
            'offlineParticipants' => $offlineParticipants,
            'cards' => $this->getCards()
        ]);
    }

    // 投票
    public function vote(Request $request, $code)
    {
        $userName = $request->input('user_name');
        $vote = $request->input('vote');

        if (!Redis::exists("room:{$code}")) {
            return response()->json(['error' => '房間不存在'], 404);
        }

        // 保存投票
        Redis::hset("votes:{$code}", $userName, $vote);
        Redis::expire("votes:{$code}", 10800);

        // 取得最新的投票和參與者資料
        $votes = Redis::hgetall("votes:{$code}");
        $participants = Redis::smembers("participants:{$code}");

        // 廣播投票更新
        \Log::info("廣播投票更新事件: {$userName} 在房間 {$code} 投票 {$vote}");
        broadcast(new \App\Events\VoteUpdated($code, $userName, $vote, $votes, $participants))->toOthers();

        return response()->json(['success' => true]);
    }

    // 開牌
    public function reveal($code)
    {
        if (!Redis::exists("room:{$code}")) {
            return response()->json(['error' => '房間不存在'], 404);
        }

        Redis::hset("room:{$code}", 'status', 'revealed');
        
        // 廣播開牌事件
        broadcast(new \App\Events\CardsRevealed($code));

        return response()->json(['success' => true]);
    }

    // 重置投票
    public function reset($code)
    {
        if (!Redis::exists("room:{$code}")) {
            return response()->json(['error' => '房間不存在'], 404);
        }

        // 只清除投票，保留參與者列表
        Redis::del("votes:{$code}");
        Redis::hset("room:{$code}", 'status', 'voting');
        
        // 廣播重置事件
        broadcast(new \App\Events\VotingReset($code));

        return response()->json(['success' => true]);
    }

    // 取消投票
    public function cancelVote($code, $userName)
    {
        if (!Redis::exists("room:{$code}")) {
            return response()->json(['error' => '房間不存在'], 404);
        }

        // 移除投票
        Redis::hdel("votes:{$code}", $userName);
        
        // 取得最新的投票和參與者資料
        $votes = Redis::hgetall("votes:{$code}");
        $participants = Redis::smembers("participants:{$code}");

        // 廣播投票更新 (用空字符串表示取消)
        \Log::info("廣播取消投票事件: {$userName} 在房間 {$code} 取消投票");
        broadcast(new \App\Events\VoteUpdated($code, $userName, '', $votes, $participants))->toOthers();

        return response()->json(['success' => true]);
    }

    // 用戶離開房間
    public function leave(Request $request, $code)
    {
        $userName = $request->input('user_name');

        if (!Redis::exists("room:{$code}") || !$userName) {
            return response()->json(['error' => '無效請求'], 400);
        }

        // 從參與者列表中移除
        Redis::srem("participants:{$code}", $userName);
        
        // 移除該用戶的投票
        Redis::hdel("votes:{$code}", $userName);
        
        // 取得最新的參與者列表
        $participants = Redis::smembers("participants:{$code}");
        
        // 廣播離開事件
        broadcast(new \App\Events\ParticipantLeft($code, $userName, $participants));

        return response()->json(['success' => true]);
    }

    // 主持人移除參與者
    public function removeParticipant(Request $request, $code)
    {
        $userName = $request->input('user_name');

        if (!Redis::exists("room:{$code}") || !$userName) {
            return response()->json(['error' => '無效請求'], 400);
        }

        // 從參與者列表中移除
        Redis::srem("participants:{$code}", $userName);
        
        // 從所有相關列表中移除
        Redis::srem("inactive:{$code}", $userName);
        Redis::srem("offline:{$code}", $userName);
        
        // 移除該用戶的投票
        Redis::hdel("votes:{$code}", $userName);
        
        // 清除用戶活動時間
        Redis::del("last_activity:{$code}:{$userName}");
        
        // 取得最新的參與者列表
        $participants = Redis::smembers("participants:{$code}");
        
        // 廣播移除事件
        broadcast(new \App\Events\ParticipantLeft($code, $userName, $participants));

        return response()->json(['success' => true]);
    }

    // 處理 "我還在房間" 訊號
    public function stillHere(Request $request)
    {
        $roomCode = $request->input('room_code');
        $userName = $request->input('user_name');
        $userType = $request->input('user_type', 'participant');
        
        if (!$roomCode || !$userName) {
            return response()->json(['error' => '缺少必要參數'], 400);
        }
        
        if (!Redis::exists("room:{$roomCode}")) {
            return response()->json(['error' => '房間不存在'], 404);
        }
        
        // 更新用戶最後活動時間
        Redis::set("last_activity:{$roomCode}:{$userName}", time());
        Redis::expire("last_activity:{$roomCode}:{$userName}", 10800); // 3 小時過期
        
        // 檢查是否為主持人
        $roomData = Redis::hgetall("room:{$roomCode}");
        $isHost = ($roomData['host'] === $userName);
        
        if (!$isHost) {
            // 只有非主持人才需要管理參與者狀態
            $isInActive = Redis::sismember("participants:{$roomCode}", $userName);
            $isInInactive = Redis::sismember("inactive:{$roomCode}", $userName);
            
            if (!$isInActive && !$isInInactive) {
                // 用戶不在任何列表中，添加到活躍列表
                Redis::sadd("participants:{$roomCode}", $userName);
                Redis::expire("participants:{$roomCode}", 10800);
                
                // 同時延長房間過期時間，確保房間持續存活
                Redis::expire("room:{$roomCode}", 10800);
            } elseif ($isInInactive) {
                // 用戶在非活躍列表中，移動到活躍列表
                Redis::srem("inactive:{$roomCode}", $userName);
                Redis::sadd("participants:{$roomCode}", $userName);
                Redis::expire("participants:{$roomCode}", 10800);
                
                // 同時延長房間過期時間，確保房間持續存活
                Redis::expire("room:{$roomCode}", 10800);
                
                // 廣播重新激活事件
                $updatedParticipants = Redis::smembers("participants:{$roomCode}");
                $updatedInactive = Redis::smembers("inactive:{$roomCode}");
                
                broadcast(new \App\Events\ParticipantReactivated($roomCode, $userName, $updatedParticipants, $updatedInactive));
            }
        } else {
            // 主持人只需要更新活動時間並延長房間過期時間
            Redis::expire("room:{$roomCode}", 10800);
        }
        
        return response()->json(['success' => true, 'timestamp' => time()]);
    }

    // 獲取房間當前狀態 (用於定期同步)
    public function getRoomState($code)
    {
        if (!Redis::exists("room:{$code}")) {
            return response()->json(['error' => '房間不存在'], 404);
        }

        $roomData = Redis::hgetall("room:{$code}");
        $votes = Redis::hgetall("votes:{$code}");
        $participants = Redis::smembers("participants:{$code}");
        
        return response()->json([
            'roomData' => $roomData,
            'votes' => $votes,
            'participants' => $participants
        ]);
    }

    // 獲取可用卡牌
    private function getCards()
    {
        return ['☕', '?', '0', '1', '2', '3', '5', '8', '13', '21', '∞'];
    }

    // 獲取預設人名列表
    private function getPredefinedNames()
    {
        return [
            'Eason',
            'Ivory',
            'Rita',
            'Tony',
            'Right',
            'Irene',
            'Emily'
        ];
    }
}