<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Throwable;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // ✅ Validação padrão (Laravel cuida do redirect + erros)
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'phone_number' => 'nullable|string|max:255',
                'password' => 'required|string|min:6|confirmed',
            ],
            [
                'name.required' => 'O nome é obrigatório.',
                'name.max' => 'O nome pode ter no máximo 255 caracteres.',

                'email.required' => 'O e-mail é obrigatório.',
                'email.email' => 'Informe um e-mail válido.',
                'email.unique' => 'Este e-mail já está em uso.',

                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
                'password.confirmed' => 'A confirmação da senha não confere.',
            ],
        );

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'] ?? null,
                'password' => Hash::make($validated['password']),
                'user_lvl' => 'user',
                'permissions' => 'user',
                'is_owner' => false,
            ]);

            // Envio do e-mail
            $user->sendEmailVerificationNotification();

            return redirect('/login')->with('status', 'Enviamos um link de verificação para o seu e-mail.');
        } catch (Throwable $e) {
            Log::error('Erro no registro (não validação)', [
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withErrors([
                    'register' => 'Erro interno ao criar conta. Tente novamente mais tarde.',
                ])
                ->withInput();
        }
    }
}
