<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use MarcReichel\IGDBLaravel\Exceptions\MissingEndpointException;

class GenerateGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:dump {--offset=} {--no-drop}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate games dump of the IGDB api';

    /**
     * Execute the console command.
     * @throws MissingEndpointException
     */
    public function handle(): void
    {
        if (!$this->option('no-drop'))
        {
            Game::query()->truncate();
            $this->warn('Games table dropped');
        }

        $current_offset = 0;
        if ($this->hasOption('offset'))
        {
            $current_offset = intval($this->option('offset'));
        }

        $i = $current_offset;
        do {
            try {
                $this->info("Trying to fetch games...");
                $games = \MarcReichel\IGDBLaravel\Models\Game::whereNotNull('game_modes')
                    ->whereNotNull('platforms')
                    ->whereNotNull('genres')
                    ->whereNotNull('player_perspectives')
                    ->whereNotNull('involved_companies')
                    ->whereNotNull('screenshots')
                    ->whereNotNull('themes')
                    ->whereNotNull('first_release_date')
                    ->whereNotNull('rating_count')
                    ->whereNotIn('platforms', Game::$_NOT_USE_PLATFORMS)
                    ->whereIn('game_type', [0])
                    ->with([
                        'genres' => [
                            'name'
                        ],
                        'platforms' => [
                            'name'
                        ],
                        'game_engines' => [
                            'name'
                        ],
                        'game_modes' => [
                            'name'
                        ],
                        'player_perspectives' => [
                            'name'
                        ],
                        'screenshots' => [
                            'url'
                        ],
                        'themes' => [
                            'name'
                        ],
                        'involved_companies' => [
                            'company.name',
                            'developer',
                            'publisher'
                        ]
                    ])
                    ->offset($current_offset)
                    ->limit(500)
                    ->get();

                $this->info("Fetched " . $games->count() . " games");
                foreach ($games as $game)
                {
                    if (!$game->screenshots || count($game->screenshots) == 0)
                    {
                        $this->warn('(#' . $i . ') Game ' . $game->name . ' ignored. No screenshot.');
                        continue;
                    }

                    $new_game = Game::create([
                        "name" => $game->name,
                        "parent_name" => $game->parent_name,
                        "game_modes" => $this->array_to_string('Modes de jeu', $game->game_modes, 'name'),
                        "platforms" => $this->array_to_string('Plateformes', $game->platforms, 'name'),
                        "genres" => $this->array_to_string('Genres', $game->genres, 'name'),
                        "themes" => $this->array_to_string('Thèmes', $game->themes, 'name'),
                        "developers" => $this->involved_companies_to_string($game->involved_companies),
                        "perspectives" => $this->array_to_string('Perspectives', $game->player_perspectives, 'name'),
                        "first_release_date" => $game->first_release_date->translatedFormat('d F Y'),
                        "screenshot" => 'http:' . $game->screenshots[0]->url,
                    ]);
                    if ($game->rating_count && $game->rating_count > 0)
                    {
                        $new_game->rating_count = $game->rating_count;
                        $new_game->save();
                    }
                    $this->info('(#' . $i . ') Game ' . $game->name . ' added to the DB');
                    $i++;
                }

                $current_offset += 500;

            }
            catch (MissingEndpointException $e)
            {
                $this->error('IGDB endpoint not found. Please check your API key.');
            }
        } while($games->count() == 500);

        $this->info("Done. " . $i . " games added.");
    }

    private function involved_companies_to_string(Collection $involved_companies): string
    {
        $companies = [];
        foreach ($involved_companies as $involved_company)
        {
            if (!$involved_company->publisher && !$involved_company->developer)
            {
                continue;
            }

            $company_name = "" . $involved_company->company->name;
            if ($involved_company->developer)
            {
                $company_name .= " (développeur)";
            }
            if ($involved_company->publisher)
            {
                $company_name .= " (éditeur)";
            }
            $companies[] = $company_name;
        }

        return "Développeurs: " . join(', ', $companies);
    }

    private function array_to_string(string $prefix, Collection $array, string $field): string
    {
        $result = [];
        foreach ($array as $element)
        {
            $result[] = $element->$field;
        }

        return $prefix . ': ' . join(', ', $result);
    }
}
