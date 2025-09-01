<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantOffline implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomCode;
    public $userName;
    public $participants;
    public $inactiveParticipants;

    public function __construct($roomCode, $userName, $participants, $inactiveParticipants)
    {
        $this->roomCode = $roomCode;
        $this->userName = $userName;
        $this->participants = $participants;
        $this->inactiveParticipants = $inactiveParticipants;
    }

    public function broadcastOn()
    {
        return new Channel('room.' . $this->roomCode);
    }

    public function broadcastAs()
    {
        return 'participant.offline';
    }

    public function broadcastWith()
    {
        return [
            'room_code' => $this->roomCode,
            'user_name' => $this->userName,
            'participants' => $this->participants,
            'inactive_participants' => $this->inactiveParticipants
        ];
    }
}