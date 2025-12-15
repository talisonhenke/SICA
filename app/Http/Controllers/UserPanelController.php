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

        $users = User::where('is_owner', false)->orderBy('name')->get();

        // NOVOS NÍVEIS
        $levels = [
            'user' => 'Usuário',
            'admin' => 'Administrador',
        ];

        return view('admin.dashboard.panels.users', compact('users', 'levels'));
    }

    /**
     * Atualizar nível do usuário (AJAX)
     */
    public function toggleAdminAjax($userId)
    {
        if (!auth()->check() || auth()->user()->user_lvl !== 'admin') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Apenas administradores podem executar esta ação.',
                ],
                403,
            );
        }

        $user = User::where('is_owner', false)->find($userId);

        if (!$user) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Usuário não encontrado ou protegido.',
                ],
                404,
            );
        }

        $user->user_lvl = $user->user_lvl === 'admin' ? 'user' : 'admin';
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Nível do usuário atualizado.',
            'user_lvl' => $user->user_lvl,
        ]);
    }

    public function blockUserAjax($userId)
    {
        if (!auth()->check() || auth()->user()->user_lvl !== 'admin') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Apenas administradores podem executar esta ação.',
                ],
                403,
            );
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Usuário não encontrado.',
                ],
                404,
            );
        }

        $user->comment_strikes = 3;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuário bloqueado para comentar.',
            'strikes' => $user->comment_strikes,
        ]);
    }

    public function resetStrikesAjax($userId)
    {
        if (!auth()->check() || auth()->user()->user_lvl !== 'admin') {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Apenas administradores podem executar esta ação.',
                ],
                403,
            );
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Usuário não encontrado.',
                ],
                404,
            );
        }

        $user->comment_strikes = 0;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Strikes zerados com sucesso.',
            'strikes' => 0,
        ]);
    }
}
