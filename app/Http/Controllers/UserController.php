<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        if(Auth::check() && Auth::user()->user_lvl === 'admin') {
            // $users = User::all();
            $users = User::where('id', '!=', 3)->get();

            $levels = [
                'member' => 'Membro',
                'moderator' => 'Moderador',
                'admin' => 'Administrador'
            ];
            return view('users.users_list', compact('users', 'levels'));
        }
        return redirect('/')->with('error', 'Você não tem permissão para acessar esta página.');
    }
    public function updateLevel(Request $request, User $user)
    {
        $request->validate([
            'user_lvl' => 'required|in:member,moderator,admin',
        ]);

        $user->user_lvl = $request->user_lvl;
        $user->save();

        return redirect()->back()->with('success', 'Nível do usuário atualizado com sucesso!');
    }
}


?>