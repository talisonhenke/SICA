<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Throwable;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'phone_number' => 'nullable|string|max:255',
                'password' => 'required|string|min:6|confirmed',
            ]
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

            // 🔐 Login automático (ESSENCIAL)
            Auth::login($user);

            // 📧 Envio do e-mail de verificação
            $user->sendEmailVerificationNotification();

            return redirect()
                ->route('verification.notice')
                ->with('status', 'Verifique seu e-mail para ativar sua conta.');
        } catch (Throwable $e) {
            Log::error('Erro no registro', ['message' => $e->getMessage()]);

            return back()
                ->withErrors(['register' => 'Erro interno ao criar conta.'])
                ->withInput();
        }
    }
}
