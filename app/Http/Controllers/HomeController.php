<?php

namespace App\Http\Controllers;

use App\Models\Room;

class HomeController extends Controller
{
    public function index(): void
    {
        $user = auth()->user();
        $rooms = Room::all();

        if ($user === null)
        {
        }
    }
}
