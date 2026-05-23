<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'no_tlp' => 'required|string|max:20',
            'password' => 'required|min:6|confirmed',
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'event_id' => 'nullable|integer|exists:events,id',
        ]);

        $fotoPath = $request->file('foto')->store('bukti_pembayaran', 'public');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_tlp' => '+62' . ltrim($request->no_tlp, '0'),
            'foto' => $fotoPath,
            'password' => Hash::make($request->password),
        ]);

        $event = null;

        if ($request->filled('event_id')) {
            $event = Event::query()
                ->whereKey($request->integer('event_id'))
                ->where('is_active', 1)
                ->first();
        }

        if (! $event) {
            $event = Event::query()
                ->where('is_active', 1)
                ->latest('id')
                ->first();
        }

        if ($event) {
            Registration::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                ],
                [
                    'fullname' => $user->name,
                    'phone' => $user->no_tlp,
                    'email' => $user->email,
                    'amount' => $event->price,
                    'foto' => $fotoPath,
                ],
            );
        }

        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/');
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
