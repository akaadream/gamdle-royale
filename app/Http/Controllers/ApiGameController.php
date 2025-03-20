<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use MarcReichel\IGDBLaravel\Builder;
use MarcReichel\IGDBLaravel\Enums\Game\Category;
use MarcReichel\IGDBLaravel\Enums\Image\Size;
use MarcReichel\IGDBLaravel\Models\Company;
use MarcReichel\IGDBLaravel\Models\Game;
use MarcReichel\IGDBLaravel\Models\GameMode;
use MarcReichel\IGDBLaravel\Models\Genre;
use MarcReichel\IGDBLaravel\Models\InvolvedCompany;
use MarcReichel\IGDBLaravel\Models\Platform;
use MarcReichel\IGDBLaravel\Models\PlayerPerspective;
use MarcReichel\IGDBLaravel\Models\Screenshot;
use MarcReichel\IGDBLaravel\Models\Theme;

class ApiGameController extends Controller
{
    private static array $_NOT_USE_PLATFORMS = [15, 16, 23, 25, 26, 27, 29, 30, 32, 35, 42, 44, 50, 51, 52, 53, 56, 57, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 77, 78, 79, 80, 82, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 127, 128, 129, 131, 132, 133, 134, 135, 136, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 128, 161, 164, 166];
    private static array $_USE_ONLY_CATEGORIES = [Category::MAIN_GAME->value, Category::REMAKE->value, Category::REMASTER->value];

    public function random_game()
    {
        return \App\Models\Game::all()->random(1);
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

    private function involved_companies_to_string(Collection $involved_companies): string
    {
        $companies = [];
        foreach ($involved_companies as $involved_company)
        {
            if (!$involved_company->publisher && !$involved_company->developer)
            {
                continue;
            }

            $company_name = "" . $involved_company->name;
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

    public function games()
    {
//        $games = \MarcReichel\IGDBLaravel\Models\Game::whereNotNull('game_modes')
//            ->whereNotNull('platforms')
//            ->whereNotNull('genres')
//            ->whereNotNull('player_perspectives')
//            ->whereNotNull('involved_companies')
//            ->whereNotNull('screenshots')
//            ->whereNotNull('themes')
//            ->whereNotNull('first_release_date')
//            ->whereNotIn('platforms', self::$_NOT_USE_PLATFORMS)
//            ->with([
//                'genres' => [
//                    'name'
//                ],
//                'platforms' => [
//                    'name'
//                ],
//                'game_engines' => [
//                    'name'
//                ],
//                'game_modes' => [
//                    'name'
//                ],
//                'player_perspectives' => [
//                    'name'
//                ],
//                'screenshots' => [
//                    'url'
//                ],
//                'themes' => [
//                    'name'
//                ],
//                'involved_companies' => [
//                    'publisher',
//                    'developer',
//                    'company.name'
//                ]
//            ])
//            ->offset(1)
//            ->get();
//
//        foreach ($games as $game)
//        {
//
////            dd($this->array_to_string('Plateformes', $game->platforms, 'name'));
////            dd('http:' . $game->screenshots[0]->url);
//            dd($this->involved_companies_to_string($game->involved_companies));
//            dd($game->genres);
//            dd($game->genres->join(', '));
//        }
//        dd($games);

        return \App\Models\Game::all(['name']);
    }
}
