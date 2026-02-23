<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Console\Command;

class AddRatingCountToGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add rating count to games';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Game::all()->each(function (Game $game) {
            if ($game->rating_count)
            {
                return;
            }
            $igdb_game = \MarcReichel\IGDBLaravel\Models\Game::where('name', $game->name)->first();
            if ($igdb_game)
            {
                if ($igdb_game->rating_count)
                {
                    $game->rating_count = $igdb_game->rating_count;
                    $game->save();
                    $this->info('Game ' . $game->name . ' updated.');
                }
            }
        });

        $this->info('Done.');
    }
}
