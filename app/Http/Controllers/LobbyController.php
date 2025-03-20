<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use MarcReichel\IGDBLaravel\Models\Game;

class LobbyController extends Controller
{
    public function index(): \Inertia\Response
    {
        $games = Game::all()->select('name')->jsonSerialize();
        return Inertia::render('Game/Lobby', [
            'games' => $games
        ]);
    }

    public function game(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
    {
        return view('game.game');
    }
}
