<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use MarcReichel\IGDBLaravel\Enums\Game\Category;
use MarcReichel\IGDBLaravel\Models\Company;

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

    private static array $_NOT_USE_PLATFORMS = [15, 16, 23, 25, 26, 27, 29, 30, 32, 35, 42, 44, 50, 51, 52, 53, 56, 57, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 77, 78, 79, 80, 82, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 131, 132, 133, 134, 135, 136, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 128, 161, 164, 166];
    private static array $_USE_ONLY_CATEGORIES = [Category::MAIN_GAME->value, Category::REMAKE->value, Category::REMASTER->value];

    /**
     * Execute the console command.
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
            $games = \MarcReichel\IGDBLaravel\Models\Game::whereNotNull('game_modes')
                ->whereNotNull('platforms')
                ->whereNotNull('genres')
                ->whereNotNull('player_perspectives')
                ->whereNotNull('involved_companies')
                ->whereNotNull('screenshots')
                ->whereNotNull('themes')
                ->whereNotNull('first_release_date')
                ->whereNotIn('platforms', self::$_NOT_USE_PLATFORMS)
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

            foreach ($games as $game)
            {
                if (!$game->screenshots || count($game->screenshots) == 0)
                {
                    $this->warn('(#' . $i . ') Game ' . $game->name . ' ignored. No screenshot.');
                    continue;
                }

                Game::create([
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
                $this->info('(#' . $i . ') Game ' . $game->name . ' added to the DB');
                $i++;
            }

            $current_offset += 500;
        } while($games->count() == 500);
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
