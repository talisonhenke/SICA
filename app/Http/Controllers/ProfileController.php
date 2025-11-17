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
            'admin' => 'Administrador'
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
            return redirect()->back()->withErrors(['current_password' => 'Senha atual incorreta.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Senha atualizada com sucesso!');
    }
}
