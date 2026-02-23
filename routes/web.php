<?php

use App\Http\Controllers\DiscordAuthController;
use App\Http\Controllers\LobbyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LobbyController::class, 'index']);
Route::get('/{roomId}', [LobbyController::class, 'game']);

Route::get('/auth/discord', [DiscordAuthController::class, 'redirect'])->name('auth.discord');
Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);
