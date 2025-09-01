<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class EventsController extends Controller
{
    public function show()
    {
        return Inertia::render('Pages', [
            'event' => [
                'id' => 1,
                'title' => '範例活動',
                'start_date' => '2025-09-15',
                'description' => '這是一個範例活動的描述內容，可以包含多行文字說明。'
            ],
        ]);
    }
}
