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

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->user_lvl = 'member';
        $user->permissions = 'user';
        $user->save(); // <-- Essa linha é que salva de fato no banco


        // Autentica o usuário
        Auth::login($user);

        // Redireciona para o dashboard
        return redirect()->intended('/'); //route to homepage
    }
}
