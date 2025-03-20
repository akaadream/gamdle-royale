<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\LobbyController::class, 'index']);
Route::get('/{roomId}', [\App\Http\Controllers\LobbyController::class, 'game']);
