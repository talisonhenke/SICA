<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

//Google Auth
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function showLoginForm(){
        return view('auth/login');
    }
    
    // Método para login com email e senha
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/'); // Redireciona para o HOMEPAGE
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas estão incorretas.',
        ]);
    }

    // Método de logout
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    // Redireciona o usuário para o Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect(); // Versão produção
    }

    // Lida com a resposta da autenticação do Google
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();// Versão proodução

            $findUser = User::where('email', $user->getEmail())->first();

            if ($findUser) {
                Auth::login($findUser);
                return redirect()->intended('/');
            } else {
                $newUser = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'user_lvl' => 'member',
                    'permissions' => 'user',
                    'password' => Hash::make(uniqid()), // Senha gerada aleatoriamente
                ]);

                Auth::login($newUser);

                return redirect()->intended('/');
            }

        } catch (\Exception $e) {
           // Em caso de erro, redirecione para a página de login
           return redirect('/login')->withErrors(['login' => 'Ocorreu um erro ao tentar fazer login com o Google.'.$e]);
        }
    }
}
