<?php

use App\Http\Controllers\DiscordAuthController;
use App\Http\Controllers\LobbyController;
use App\Models\Room;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return Room::generateCode();
});

/**
 * Authentication routes
 */
Route::get('/auth/discord', [DiscordAuthController::class, 'redirect'])->name('auth.discord');
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);
Route::post('/logout', [DiscordAuthController::class, 'logout'])->name('logout');

/**
 * Game routes
 */
Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/game/{roomId}', 'pages::game')->name('game');
