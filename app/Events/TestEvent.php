<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TestEvent implements ShouldBroadcast
{
    public $message;

    public function __construct($message = '測試成功！')
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('test-channel');
    }

    public function broadcastAs()
    {
        return 'TestEvent';
    }
}
