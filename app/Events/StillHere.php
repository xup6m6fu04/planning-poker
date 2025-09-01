<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StillHere implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomCode;
    public $userName;
    public $userType;

    public function __construct($roomCode, $userName, $userType = 'participant')
    {
        $this->roomCode = $roomCode;
        $this->userName = $userName;
        $this->userType = $userType;
    }

    public function broadcastOn()
    {
        return new Channel('room.' . $this->roomCode);
    }

    public function broadcastAs()
    {
        return 'user.still-here';
    }

    public function broadcastWith()
    {
        return [
            'room_code' => $this->roomCode,
            'user_name' => $this->userName,
            'user_type' => $this->userType,
            'timestamp' => time()
        ];
    }
}