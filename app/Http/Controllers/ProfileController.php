<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Exibe a view de edição do perfil
    public function editProfile()
    {
        $user = Auth::user();
        $levels = [
            'member' => 'Membro',
            'moderator' => 'Moderador',
            'admin' => 'Administrador',
        ];

        return view('profile.edit_profile', [
            'user' => $user,
            'levels' => $levels,
            'addresses' => $user->addresses, // <-- Adicionamos isto!
        ]);
    }

    // Atualiza o nome do usuário logado
    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        return redirect()->back()->with('success', 'Nome atualizado com sucesso!');
    }

    // Atualiza a senha do usuário logado
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        // Verifica se a senha atual confere
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()
                ->back()
                ->withErrors(['current_password' => 'Senha atual incorreta.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Senha atualizada com sucesso!');
    }
    // Atualiza o telefone do usuário logado
    public function updatePhone(Request $request)
    {
        // O usuário digita formatado, então validamos o formato (99) 9 9999-9999
        $request->validate([
            'phone_number' => ['required', 'regex:/^\(\d{2}\)\s\d\s\d{4}-\d{4}$/'],
        ]);

        // Remove todos os caracteres que não são dígitos
        $rawPhone = preg_replace('/\D/', '', $request->phone_number);

        // Deve ter exatamente 11 dígitos
        if (strlen($rawPhone) !== 11) {
            return redirect()
                ->back()
                ->withErrors([
                    'phone_number' => 'Telefone inválido.',
                ])
                ->withInput();
        }

        $user = Auth::user();
        $user->phone_number = $rawPhone;
        $user->save();

        return redirect()->back()->with('success', 'Telefone atualizado com sucesso!');
    }
}
