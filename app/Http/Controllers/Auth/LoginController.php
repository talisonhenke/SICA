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
        // ✅ Validação clássica com mensagens traduzidas
        $validated = $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required|min:6',
            ],
            [
                'email.required' => 'O email é obrigatório.',
                'email.email' => 'Informe um email válido.',
                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            ],
        );

        // Usa apenas dados validados
        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        // ❌ Credenciais inválidas
        if (!Auth::attempt($credentials)) {
            return back()
                ->withErrors([
                    'email' => 'Email ou senha incorretos.',
                ])
                ->withInput($request->only('email'));
        }

        $user = Auth::user();

        // ❌ Email não verificado
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();

            return back()
                ->withErrors([
                    'email' => 'Você precisa verificar seu e-mail antes de fazer login.',
                ])
                ->withInput($request->only('email'));
        }

        // ✅ Redirecionamento por nível
        if ($user->user_lvl === 'admin') {
            return redirect()->intended('/admin/ajax/dashboard');
        }

        return redirect()->intended('/');
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
                $user->email_verified_at = now(); // ✅ VERIFICADO AUTOMATICAMENTE
                $user->user_lvl = 'user';
                $user->permissions = 'user';
                $user->is_owner = false;
                $user->password = Hash::make(uniqid());
                $user->save();
            } else {
                // Caso o usuário já exista mas não esteja verificado
                if (!$user->hasVerifiedEmail()) {
                    $user->email_verified_at = now();
                    $user->save();
                }
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
