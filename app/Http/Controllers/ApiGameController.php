<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use MarcReichel\IGDBLaravel\Exceptions\MissingEndpointException;
use MarcReichel\IGDBLaravel\Models\Game;

class ApiGameController extends Controller
{
    /**
     * Get a random game
     * @return JsonResponse
     */
    public function random_game()
    {
        return response()->json(\App\Models\Game::where('rating_count', '>', 30)->get()->random(1)->first());
    }

    /**
     * Get the list of all game's names
     * @return JsonResponse
     */
    public function games(): JsonResponse
    {
        return response()->json(\App\Models\Game::select('name')->where('rating_count', '>', 30)->distinct()->get());
    }

    public function filter_games(string $filter): JsonResponse
    {
        return response()->json(\App\Models\Game::select('name')->where('rating_count', '>', 30)->whereLike('name', '%' . $filter . '%')->distinct()->get());
    }

    /**
     * To test some requests
     * @return JsonResponse
     */
    public function test(): JsonResponse
    {
        $game = Game::all()->where('rating_count', '>', '100')->random(1)->first();
        return response()->json($game);
    }

    /**
     * Get the number of entries in the games table
     * @return int
     * @throws \MarcReichel\IGDBLaravel\Exceptions\MissingEndpointException
     */
    public function count(): int
    {
        return \MarcReichel\IGDBLaravel\Models\Game::whereNotNull('game_modes')
            ->whereNotNull('platforms')
            ->whereNotNull('genres')
            ->whereNotNull('player_perspectives')
            ->whereNotNull('involved_companies')
            ->whereNotNull('screenshots')
            ->whereNotNull('themes')
            ->whereNotNull('first_release_date')
            ->whereNotNull('rating_count')
            ->whereNotIn('platforms', \App\Models\Game::$_NOT_USE_PLATFORMS)
            ->whereIn('category', \App\Models\Game::$_USE_ONLY_CATEGORIES)
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
            ->count();
    }

    /**
     * @throws MissingEndpointException
     */
    public function shaper()
    {
        return Game::where('name', 'Shaper')->first();
    }
}
