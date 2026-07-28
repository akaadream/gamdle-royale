<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;

class LobbyController extends Controller
{
    public function index(): void
    {
        $games = Game::select('name')->where('rating_count', '>', 30)->distinct()->get();
    }

    public function game(string $roomId): void
    {
        $games = Game::select('name')->where('rating_count', '>', 30)->distinct()->get();
    }
}
