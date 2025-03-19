<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MarcReichel\IGDBLaravel\Enums\Game\Category;
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
        $game = Game::all()->whereNotNull('game_modes')
            ->whereNotNull('platforms')
            ->whereNotNull('genres')
            ->whereNotNull('player_perspectives')
            ->whereNotNull('involved_companies')
            ->whereNotNull('screenshots')
            ->whereNotNull('themes')
            ->whereNotInStrict('platforms', self::$_NOT_USE_PLATFORMS)
            ->random(1)
            ->first();

        if ($game)
        {
            // dump("Name: " . $game->name);
            $parent_game_name = "";
            if ($game->parent_game)
            {
                $parent_game = Game::findOrFail($game->parent_game);
                $parent_game_name = $parent_game->name;
                // dump("Parent Game: " . $parent_game->name);
            }

            // dump($this->array_to_string('Modes de jeu', $game->game_modes, GameMode::class, 'name'));
            // dump($this->array_to_string('Plateformes', $game->platforms, Platform::class, 'name'));
            // dump($this->array_to_string('Genres', $game->genres, Genre::class, 'name'));
            // dump($this->array_to_string('Thèmes', $game->themes, Theme::class, 'name'));
            // dump($this->array_to_string('Développeurs', $game->involved_companies, InvolvedCompany::class, 'name', Company::class, 'company', 'name'));
            // dump($this->array_to_string('Perspectives', $game->player_perspectives, PlayerPerspective::class, 'name'));

            $screenshot = Screenshot::findOrFail($game->screenshots[array_rand($game->screenshots)]);
            // dump("Screenshot: " . $screenshot->url);
            // dd("Première date de sortie: " . $game->first_release_date->translatedFormat('d F Y'));

            return [
                "name" => $game->name,
                "parent_name" => $parent_game_name,
                "game_modes" => $this->array_to_string('Modes de jeu', $game->game_modes, GameMode::class, 'name'),
                "platformers" => $this->array_to_string('Plateformes', $game->platforms, Platform::class, 'name'),
                "genres" => $this->array_to_string('Genres', $game->genres, Genre::class, 'name'),
                "themes" => $this->array_to_string('Thèmes', $game->themes, Theme::class, 'name'),
                "developers" => $this->array_to_string('Développeurs', $game->involved_companies, InvolvedCompany::class, 'name', Company::class, 'company', 'name'),
                "perspectives" => $this->array_to_string('Perspectives', $game->player_perspectives, PlayerPerspective::class, 'name'),
                "first_release_date" => $game->first_release_date->translatedFormat('d F Y'),
                "screenshot" => "https:" . $screenshot->url
            ];

//            dd($game);
        }
        else
        {
            dd("No game found");
        }
    }

    private function array_to_string(string $prefix, array $array, string $model, string $field, string|null $sub_model = null, string $sub_field = '', string $sub_model_field = ''): string
    {
        if (!$array)
        {
            return "";
        }

        $result = [];
        foreach ($array as $element)
        {
            $element_instance = $model::findOrFail($element);
            if ($sub_model)
            {
                $sub_model_instance = $sub_model::findOrFail($element_instance->$sub_field);
                $result[] = $sub_model_instance->$sub_model_field;
            }
            else
            {
                $result[] = $element_instance->$field;
            }
        }

        return $prefix . ': ' . join(', ', $result);
    }

    private function involved_companies_to_string($involved_companies): string
    {
        if ($involved_companies)
        {
            $companies = [];
            foreach ($involved_companies as $involved_company)
            {

                $involved_company_instance = InvolvedCompany::findOrFail($involved_company);

                if (!$involved_company_instance->publisher && !$involved_company_instance->developer)
                {
                    continue;
                }

                $company_instance = Company::findOrFail($involved_company_instance->company);
                $company_name = "" . $company_instance->name;
                if ($involved_company_instance->developer)
                {
                    $company_name .= " (développeur)";
                }
                if ($involved_company_instance->publisher)
                {
                    $company_name .= " (éditeur)";
                }
                $companies[] = $company_name;
            }

            return "Développeurs: " . join(', ', $companies);
        }

        return "";
    }

    private function old_code()
    {
        //            if ($game->game_modes)
//            {
//                $modes = [];
//                foreach ($game->game_modes as $game_mode)
//                {
//                    $game_mode_instance = \MarcReichel\IGDBLaravel\Models\GameMode::findOrFail($game_mode);
//                    $modes[] = $game_mode_instance->name;
//                }
//
//                dump("Game Modes: " . join(', ', $modes));
//            }

//            if ($game->platforms)
//            {
//                $platforms = [];
//                foreach ($game->platforms as $platform)
//                {
//                    $platform_instance = \MarcReichel\IGDBLaravel\Models\Platform::findOrFail($platform);
//                    $platforms[] = $platform_instance->name;
//                }
//
//                dump("Platforms: " . join(', ', $platforms));
//            }


//            if ($game->genres)
//            {
//                $genres = [];
//                foreach ($game->genres as $genre)
//                {
//                    $genre_instance = Genre::findOrFail($genre);
//                    $genres[] = $genre_instance->name;
//                }
//
//                dump("Genres: " . join(', ', $genres));
//            }

//            if ($game->themes)
//            {
//                $themes = [];
//                foreach ($game->themes as $theme)
//                {
//                    $theme_instance = Theme::findOrFail($theme);
//                    $themes[] = $theme_instance->name;
//                }
//
//                dump("Themes: " . join(', ', $themes));
//            }

        //            if ($game->player_perspectives)
//            {
//                $perspectives = [];
//                foreach ($game->player_perspectives as $perspective)
//                {
//                    $perspective_instance = PlayerPerspective::findOrFail($perspective);
//                    $perspectives[] = $perspective_instance->name;
//                }
//
//                dump("Perspectives: " . join(', ', $perspectives));
//            }
    }
}
