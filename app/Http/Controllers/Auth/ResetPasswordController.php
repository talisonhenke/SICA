<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed',
            ],
            [
                'password.confirmed' => 'As senhas não conferem.',
                'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            ]
        );

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect('/login')->with('status', 'Senha redefinida com sucesso!')
            : back()->withErrors(['email' => 'Token inválido ou expirado.']);
    }
}
