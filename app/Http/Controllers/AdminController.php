<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\TopicComment;
use App\Models\PlantComment;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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

        // Comentários de TÓPICOS que precisam de moderação (não moderados E (tox >= 0.4 OR reported))
        $topicNew = TopicComment::where('moderated', 0)
            ->where(function ($query) {
                $query->where('toxicity_level', '>=', 0.4)->orWhere('reported', 1);
            })
            ->count();

        // Comentários de PLANTAS que precisam de moderação (mesma lógica)
        $plantNew = PlantComment::where('moderated', 0)
            ->where(function ($query) {
                $query->where('toxicity_level', '>=', 0.4)->orWhere('reported', 1);
            })
            ->count();

        // Soma total (cada registro é contado apenas uma vez por tabela)
        $newModeration = $topicNew + $plantNew;

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
                $query->where('toxicity_level', '>=', 0.7)->orWhere('reported', 1);
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

        /*
    |--------------------------------------------------------------------------
    | 1) BASE QUERY — TOPIC COMMENTS
    |--------------------------------------------------------------------------
    */
        $topicQuery = TopicComment::with('user', 'topic')
            ->where('moderated', 0)
            ->where(function ($q) {
                $q->where('toxicity_level', '>=', 0.1)->orWhere('reported', 1);
            });

        /*
    |--------------------------------------------------------------------------
    | 2) BASE QUERY — PLANT COMMENTS
    |--------------------------------------------------------------------------
    */
        $plantQuery = PlantComment::with('user', 'plant')
            ->where('moderated', 0)
            ->where(function ($q) {
                $q->where('toxicity_level', '>=', 0.1)->orWhere('reported', 1);
            });

        /*
    |--------------------------------------------------------------------------
    | 3) FILTROS
    |--------------------------------------------------------------------------
    */
        switch ($filter) {
            case 'suspect': // >= 0.1
                $topicQuery->where('toxicity_level', '>=', 0.1);
                $plantQuery->where('toxicity_level', '>=', 0.1);
                break;

            case 'high': // >= 0.7
                $topicQuery->where('toxicity_level', '>=', 0.7);
                $plantQuery->where('toxicity_level', '>=', 0.7);
                break;

            case 'reported':
                $topicQuery->where('reported', 1);
                $plantQuery->where('reported', 1);
                break;

            default:
                // "all": já tem as regras globais aplicadas
                break;
        }

        /*
    |--------------------------------------------------------------------------
    | 4) CARREGA TODOS OS COMENTÁRIOS
    |--------------------------------------------------------------------------
    */
        $topicComments = $topicQuery->get();
        $plantComments = $plantQuery->get();

        $topicComments->map(function ($c) {
            $c->comment_type = 'topic';
            return $c;
        });

        $plantComments->map(function ($c) {
            $c->comment_type = 'plant';
            return $c;
        });

        // junta tudo em uma única coleção
        

        /*
    |--------------------------------------------------------------------------
    | 5) UNE AS DUAS LISTAS
    |--------------------------------------------------------------------------
    */
        //$allComments = $topicComments->merge($plantComments)->sortByDesc('created_at')->values(); // reseta indexes
        $allComments = $topicComments->merge($plantComments)->sortByDesc('created_at')->values();

        /*
    |--------------------------------------------------------------------------
    | 6) PAGINAÇÃO MANUAL
    |--------------------------------------------------------------------------
    */
        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = $allComments->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $comments = new LengthAwarePaginator($items, $allComments->count(), $perPage, $currentPage, ['path' => request()->url(), 'query' => request()->query()]);

        return view('admin.moderation.index', compact('comments', 'filter'));
    }

    public function moderationIndexAjax(Request $request)
    {
        [$comments, $filter] = $this->buildModerationList($request);

        return view('admin.moderation.ajax.index', compact('comments', 'filter'));
    }

    protected function buildModerationList(Request $request)
    {
        $filter = $request->input('filter', 'all');

        // TOPIC
        $topicQuery = TopicComment::with('user', 'topic')
            ->where('moderated', 0)
            ->where(fn ($q) =>
                $q->where('toxicity_level', '>=', 0.1)
                  ->orWhere('reported', 1)
            );

        // PLANT
        $plantQuery = PlantComment::with('user', 'plant')
            ->where('moderated', 0)
            ->where(fn ($q) =>
                $q->where('toxicity_level', '>=', 0.1)
                  ->orWhere('reported', 1)
            );

        // filtros
        match ($filter) {
            'suspect' => [$topicQuery, $plantQuery]->each->where('toxicity_level', '>=', 0.1),
            'high'    => [$topicQuery, $plantQuery]->each->where('toxicity_level', '>=', 0.7),
            'reported'=> [$topicQuery, $plantQuery]->each->where('reported', 1),
            default   => null,
        };

        $topic = $topicQuery->get()->map(fn ($c) => tap($c)->comment_type = 'topic');
        $plant = $plantQuery->get()->map(fn ($c) => tap($c)->comment_type = 'plant');

        $all = $topic->merge($plant)->sortByDesc('created_at')->values();

        // paginação manual
        $perPage = 20;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $all->slice(($page - 1) * $perPage, $perPage)->values();

        $comments = new LengthAwarePaginator(
            $items,
            $all->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return [$comments, $filter];
    }
}
