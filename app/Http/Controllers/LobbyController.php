<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LobbyController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
    {
        return view('game.lobby');
    }

    public function game(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
    {
        return view('game.game');
    }
}
