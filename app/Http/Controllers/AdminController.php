<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\TopicComment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Verifica atualizações em pedidos e moderação.
     * Usado para exibir números na navbar.
     */
    public function checkUpdates()
    {
        // Número de pedidos pendentes
        $newOrders = Order::where('status', 'pending')->count();

        // Comentários novos que precisam de moderação:
        // - Não moderados
        // - E (toxic >= 0.1 ou reported)
        $newModeration = TopicComment::where('moderated', 0)
            ->where(function ($query) {
                $query->where('toxicity_level', '>=', 0.4)
                      ->orWhere('reported', 1);
            })
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        return response()->json([
            'orders' => $newOrders,
            'moderation' => $newModeration,
        ]);
    }

    /**
     * Contadores estáticos para exibir na dashboard.
     */
    public function counters()
    {
        $pendingOrders = Order::where('status', 'pending')->count();

        // Fila completa de moderação:
        // Comentários que estão:
        // - Não moderados
        // - Toxic >= 0.7 OU Reported
        $moderationQueue = TopicComment::where('moderated', 0)
            ->where(function ($query) {
                $query->where('toxicity_level', '>=', 0.7)
                      ->orWhere('reported', 1);
            })
            ->count();

        return response()->json([
            'pending_orders' => $pendingOrders,
            'moderation_queue' => $moderationQueue,
        ]);
    }

    /**
     * Tela da lista de moderação com filtros.
     */
    public function moderationIndex(Request $request)
    {
        $filter = $request->input('filter', 'all');

        $query = TopicComment::with('user', 'topic')
            ->where('moderated', 0) // regra global
            ->where(function ($q) {
                $q->where('toxicity_level', '>=', 0.1)
                  ->orWhere('reported', 1);
            });

        switch ($filter) {
            case 'suspect': // >= 0.1
                $query->where('toxicity_level', '>=', 0.1);
                break;

            case 'high': // >= 0.7
                $query->where('toxicity_level', '>=', 0.7);
                break;

            case 'reported':
                $query->where('reported', 1);
                break;

            default:
                // "all": não mexe, já está filtrado pela regra geral
                break;
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.moderation.index', compact('comments', 'filter'));
    }
}
