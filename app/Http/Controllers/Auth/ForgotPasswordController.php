<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(
            ['email' => 'required|email'],
            [
                'email.required' => 'Informe seu e-mail.',
                'email.email' => 'Informe um e-mail válido.',
            ]
        );

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Nenhum usuário encontrado com esse e-mail.',
            ]);
        }

        // 🔒 REGRA IMPORTANTE
        if (!$user->hasVerifiedEmail()) {
            return back()->withErrors([
                'email' => 'Você precisa verificar seu e-mail antes de recuperar a senha.',
            ]);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Link de recuperação enviado para seu e-mail.')
            : back()->withErrors(['email' => 'Erro ao enviar link de recuperação.']);
    }
}
