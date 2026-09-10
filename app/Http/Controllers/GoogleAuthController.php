<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        if ($request->filled('event_id')) {
            $request->session()->put('oauth_intended_event_id', $request->integer('event_id'));
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Login dengan Google gagal, silakan coba lagi.');
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            if (! $user->google_id) {
                $user->google_id = $googleUser->getId();
                $user->save();
            }
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => Hash::make(Str::random(32)),
                'role' => 'user',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        $eventId = $request->session()->pull('oauth_intended_event_id');

        if ($eventId && Event::whereKey($eventId)->exists()) {
            return redirect()->route('events.show', $eventId)
                ->with('success', 'Login berhasil! Silakan lanjutkan pendaftaran event.');
        }

        return redirect()->route('home')->with('success', 'Login berhasil!');
    }
}
