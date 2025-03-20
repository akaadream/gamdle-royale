<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;

class LobbyController extends Controller
{
    public function index(): \Inertia\Response
    {
        $games = Game::select('name')->where('rating_count', '>', 30)->distinct()->get();
        return Inertia::render('Game/Lobby', [
            'games' => $games,
            'joinOnly' => false,
            'id' => ''
        ]);
    }

    public function game(string $roomId): \Inertia\Response
    {
        $games = Game::select('name')->where('rating_count', '>', 30)->distinct()->get();
        return Inertia::render('Game/Lobby', [
            'games' => $games,
            'joinOnly' => true,
            'id' => $roomId
        ]);
    }
}
