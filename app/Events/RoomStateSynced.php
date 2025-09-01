<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomStateSynced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomCode;
    public $roomData;
    public $votes;
    public $participants;
    public $inactiveParticipants;
    public $offlineParticipants;

    public function __construct($roomCode, $roomData, $votes, $participants, $inactiveParticipants = [], $offlineParticipants = [])
    {
        $this->roomCode = $roomCode;
        $this->roomData = $roomData;
        $this->votes = $votes;
        $this->participants = $participants;
        $this->inactiveParticipants = $inactiveParticipants;
        $this->offlineParticipants = $offlineParticipants;
    }

    public function broadcastOn()
    {
        return new Channel('room.' . $this->roomCode);
    }

    public function broadcastAs()
    {
        return 'room.state.synced';
    }

    public function broadcastWith()
    {
        return [
            'roomData' => $this->roomData,
            'votes' => $this->votes,
            'participants' => $this->participants,
            'inactive_participants' => $this->inactiveParticipants,
            'offline_participants' => $this->offlineParticipants,
        ];
    }
}