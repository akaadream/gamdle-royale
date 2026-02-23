<?php

use Illuminate\Support\Facades\Route;

Route::get('random-game', [\App\Http\Controllers\ApiGameController::class, 'random_game']);
Route::get('games', [\App\Http\Controllers\ApiGameController::class, 'games']);
Route::get('games/{filter}', [\App\Http\Controllers\ApiGameController::class, 'filter_games']);
Route::get('test', [\App\Http\Controllers\ApiGameController::class, 'test']);
Route::get('count', [\App\Http\Controllers\ApiGameController::class, 'count']);
Route::get('shaper', [\App\Http\Controllers\ApiGameController::class, 'shaper']);
