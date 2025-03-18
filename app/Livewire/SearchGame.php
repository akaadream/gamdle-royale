<?php

namespace App\Livewire;

use Livewire\Component;
use MarcReichel\IGDBLaravel\Models\Game;

class SearchGame extends Component
{
    public string $search = "";

    public function render()
    {
        $games = [];
        if ($this->search) {
            $query = Game::whereLike('name', '%' . $this->search . '%');
            $games = $query->get();
        }

        return view('livewire.search-game', [
            'games' => $games
        ]);
    }
}
