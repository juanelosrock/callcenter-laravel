<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'     => ['required', 'email'],
            'password'  => ['required'],
            'extension' => ['required', 'integer', 'min:0'],
        ]);

        $extension = $credentials['extension'];
        unset($credentials['extension']);

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if ($user && !$user->active) {
            return back()->withErrors(['email' => 'Tu cuenta está desactivada.'])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            Auth::user()->update(['extension' => $extension]);
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
