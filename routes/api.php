<?php

use Illuminate\Support\Facades\Route;

Route::get('random-game', [\App\Http\Controllers\ApiGameController::class, 'random_game']);
Route::get('games', [\App\Http\Controllers\ApiGameController::class, 'games']);
