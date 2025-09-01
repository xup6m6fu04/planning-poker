<?php

use App\Http\Controllers\EventsController;
use App\Http\Controllers\PlanningPokerController;
use Illuminate\Support\Facades\Route;

// Planning Poker 路由
Route::get('/', [PlanningPokerController::class, 'index'])->name('poker.index');
Route::post('/create-room', [PlanningPokerController::class, 'createRoom'])->name('poker.create');
Route::post('/join-room', [PlanningPokerController::class, 'joinRoom'])->name('poker.join');
Route::get('/room/{code}', [PlanningPokerController::class, 'room'])->name('poker.room');
Route::get('/host/{code}', [PlanningPokerController::class, 'host'])->name('poker.host');
Route::post('/join-host', [PlanningPokerController::class, 'joinHost'])->name('poker.join-host');

// API 路由
Route::post('/api/vote/{code}', [PlanningPokerController::class, 'vote'])->name('poker.vote');
Route::delete('/api/vote/{code}/{userName}', [PlanningPokerController::class, 'cancelVote'])->name('poker.cancel-vote');
Route::post('/api/reveal/{code}', [PlanningPokerController::class, 'reveal'])->name('poker.reveal');
Route::post('/api/reset/{code}', [PlanningPokerController::class, 'reset'])->name('poker.reset');
Route::post('/api/leave/{code}', [PlanningPokerController::class, 'leave'])->name('poker.leave');
Route::post('/api/remove-participant/{code}', [PlanningPokerController::class, 'removeParticipant'])->name('poker.remove-participant');
Route::post('/api/still-here', [PlanningPokerController::class, 'stillHere'])->name('poker.still-here');
Route::get('/api/room-state/{code}', [PlanningPokerController::class, 'getRoomState'])->name('poker.room-state');

// 原有的事件路由
Route::get('/event', [EventsController::class, 'show']);