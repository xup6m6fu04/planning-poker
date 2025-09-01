<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoteUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomCode;
    public $userName;
    public $vote;
    public $votes;
    public $participants;

    public function __construct($roomCode, $userName, $vote, $votes, $participants)
    {
        $this->roomCode = $roomCode;
        $this->userName = $userName;
        $this->vote = $vote;
        $this->votes = $votes;
        $this->participants = $participants;
    }

    public function broadcastOn()
    {
        return new Channel('room.' . $this->roomCode);
    }

    public function broadcastAs()
    {
        return 'vote.updated';
    }

    public function broadcastWith()
    {
        return [
            'user_name' => $this->userName,
            'vote' => $this->vote,
            'votes' => $this->votes,
            'participants' => $this->participants,
        ];
    }
}