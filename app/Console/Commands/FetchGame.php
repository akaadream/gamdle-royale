<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MarcReichel\IGDBLaravel\Exceptions\MissingEndpointException;
use MarcReichel\IGDBLaravel\Models\Game;

class FetchGame extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:fetch {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch a game from IGDB';

    /**
     * Execute the console command.
     * @throws MissingEndpointException
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $game = Game::where('name', $name)->first();
        $this->info($game->name . ' released: ' . $game->first_release_date . ', developed by: ' . $game->developers);
    }
}
