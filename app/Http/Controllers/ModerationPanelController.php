<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TopicComment;
use App\Models\PlantComment;

class ModerationPanelController extends Controller
{
    public function index(Request $request)
    {
        /*
        |------------------------------------------------------------------
        | FILTRO
        |------------------------------------------------------------------
        */
        $filter = $request->input('filter', 'all');

        /*
        |------------------------------------------------------------------
        | CONTADOR DE MODERAÇÃO (MENU / HEADER DO PAINEL)
        |------------------------------------------------------------------
        */
        $topicCount = TopicComment::where('moderated', 0)
            ->where(fn ($q) =>
                $q->where('toxicity_level', '>=', 0.1)
                  ->orWhere('reported', 1)
            )
            ->count();

        $plantCount = PlantComment::where('moderated', 0)
            ->where(fn ($q) =>
                $q->where('toxicity_level', '>=', 0.1)
                  ->orWhere('reported', 1)
            )
            ->count();

        $moderationCount = $topicCount + $plantCount;

        /*
        |------------------------------------------------------------------
        | BASE QUERY — TOPIC COMMENTS
        |------------------------------------------------------------------
        */
        $topicQuery = TopicComment::with('user', 'topic')
            ->where('moderated', 0)
            ->where(fn ($q) =>
                $q->where('toxicity_level', '>=', 0.1)
                  ->orWhere('reported', 1)
            );

        /*
        |------------------------------------------------------------------
        | BASE QUERY — PLANT COMMENTS
        |------------------------------------------------------------------
        */
        $plantQuery = PlantComment::with('user', 'plant')
            ->where('moderated', 0)
            ->where(fn ($q) =>
                $q->where('toxicity_level', '>=', 0.1)
                  ->orWhere('reported', 1)
            );

        /*
        |------------------------------------------------------------------
        | APLICA FILTROS
        |------------------------------------------------------------------
        */
        switch ($filter) {
            case 'suspect':
                $topicQuery->where('toxicity_level', '>=', 0.1);
                $plantQuery->where('toxicity_level', '>=', 0.1);
                break;

            case 'high':
                $topicQuery->where('toxicity_level', '>=', 0.7);
                $plantQuery->where('toxicity_level', '>=', 0.7);
                break;

            case 'reported':
                $topicQuery->where('reported', 1);
                $plantQuery->where('reported', 1);
                break;

            default:
                // all
                break;
        }

        /*
        |------------------------------------------------------------------
        | CARREGA COMENTÁRIOS
        |------------------------------------------------------------------
        */
        $topicComments = $topicQuery->get()->map(function ($c) {
            $c->comment_type = 'topic';
            return $c;
        });

        $plantComments = $plantQuery->get()->map(function ($c) {
            $c->comment_type = 'plant';
            return $c;
        });

        /*
        |------------------------------------------------------------------
        | UNE E ORDENA (SEM PAGINAÇÃO)
        |------------------------------------------------------------------
        */
        $comments = $topicComments
            ->merge($plantComments)
            ->sortByDesc('created_at')
            ->values();

        /*
        |------------------------------------------------------------------
        | VIEW DO PAINEL
        |------------------------------------------------------------------
        */
        return view(
            'admin.dashboard.panels.moderation',
            compact(
                'comments',
                'filter',
                'moderationCount'
            )
        );
    }
}
