<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegisterController extends Controller
{
    // Método para registro convencional
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Cria o novo usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'user_lvl' => 'member',
            'permissions' => 'user',
            'password' => Hash::make($request->password_confirmation), //passwond_confirmation id
        ]);

        // Autentica o usuário
        Auth::login($user);

        // Redireciona para o dashboard
        return redirect()->intended('/'); //route to homepage
    }
}
