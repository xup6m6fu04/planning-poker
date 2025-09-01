<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantJoined implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomCode;
    public $userName;
    public $participants;

    public function __construct($roomCode, $userName, $participants)
    {
        $this->roomCode = $roomCode;
        $this->userName = $userName;
        $this->participants = $participants;
    }

    public function broadcastOn()
    {
        return new Channel('room.' . $this->roomCode);
    }

    public function broadcastAs()
    {
        return 'participant.joined';
    }

    public function broadcastWith()
    {
        return [
            'user_name' => $this->userName,
            'participants' => $this->participants,
        ];
    }
}