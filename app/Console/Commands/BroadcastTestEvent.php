<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\TestBroadcastEvent;

class BroadcastTestEvent extends Command
{
    protected $signature = 'broadcast:test';
    protected $description = '透過 Reverb 廣播測試事件';

    public function handle(): int
    {
        broadcast(new TestBroadcastEvent('從 CLI 廣播測試成功！'));
        $this->info('✅ 廣播事件已送出！');
        return Command::SUCCESS;
    }
}

