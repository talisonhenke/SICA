<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth/login');
    }

    // Login com email e senha
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Você precisa verificar seu e-mail antes de fazer login.',
                ]);
            }

            if ($user->user_lvl === 'admin') {
                return redirect()->intended('/admin/ajax/dashboard');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas estão incorretas.',
        ]);
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    // Redireciona para o Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Callback do Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = new User();
                $user->name = $googleUser->getName();
                $user->email = $googleUser->getEmail();
                $user->user_lvl = 'user';
                $user->permissions = 'user';
                $user->is_owner = false;
                $user->password = Hash::make(uniqid());
                $user->save();
            }

            Auth::login($user);

            // Redirecionamento por nível
            if ($user->user_lvl === 'admin') {
                return redirect()->intended('/admin/ajax/dashboard');
            }

            return redirect()->intended('/');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors([
                'login' => 'Ocorreu um erro ao tentar fazer login com o Google.',
            ]);
        }
    }
}
