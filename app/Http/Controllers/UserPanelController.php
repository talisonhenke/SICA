<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserPanelController extends Controller
{
    /**
     * Painel de usuários (LISTAGEM)
     */
    public function index()
    {
        // Segurança extra (dashboard já deve estar protegido, mas não custa)
        if (!Auth::check() || Auth::user()->user_lvl !== 'admin') {
            abort(403);
        }

        $users = User::where('is_owner', false)
            ->orderBy('name')
            ->get();

        // NOVOS NÍVEIS
        $levels = [
            'user'  => 'Usuário',
            'admin' => 'Administrador',
        ];

        return view('admin.dashboard.panels.users', compact('users', 'levels'));
    }

    /**
     * Atualizar nível do usuário (AJAX)
     */
    public function updateLevel(Request $request, User $user)
    {
        if (!Auth::check() || Auth::user()->user_lvl !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Permissão negada.'
            ], 403);
        }

        $request->validate([
            'user_lvl' => 'required|in:user,admin',
        ]);

        try {
            DB::beginTransaction();

            $user->user_lvl = $request->user_lvl;
            $user->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Nível do usuário atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar nível do usuário.'
            ], 500);
        }
    }
}
