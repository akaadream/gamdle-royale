<?php

namespace App\Http\Controllers;

use App\Enums\AuthServices;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class DiscordAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('discord')->redirect();
    }

    public function callback()
    {
        $discord = Socialite::driver('discord')->user();
        $user = User::where('service', AuthServices::Discord)
            ->where('service_id', $discord->id)
            ->first();

        if (!$user)
        {
            $user = User::create([
                'name' => $discord->name ?? $discord->nickname,
                'email' => $discord->email,
                'service' => AuthServices::Discord,
                'service_id' => $discord->id,
                'avatar' => $discord->avatar,
                'password' => bcrypt(Str::random(32))
            ]);
        }

        Auth::login($user, true);
        return redirect()->route('home');
    }
}
