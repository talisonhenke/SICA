<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SiteReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SiteReviewPanelController extends Controller
{
    /**
     * Painel de avaliações (VISUALIZAÇÃO)
     */
    public function index()
    {
        // Marca todas como lidas
        SiteReview::where('new_reviews', 'unread')->update(['new_reviews' => 'read']);

        // Pega todas as avaliações sem paginação
        $reviews = SiteReview::with('user')->get();

        // Calcula média
        $average = SiteReview::avg('rating');

        // Arredondamento estilo 4.0, 4.5, 4.0
        $rounded = 0;
        if ($average !== null) {
            $int = floor($average);
            $decimal = $average - $int;

            $rounded = $decimal < 0.5 ? $int : $int + 0.5;
        }

        return view('admin.dashboard.panels.reviews', [
            'reviews' => $reviews,
            'average' => $average,
            'rounded' => $rounded,
        ]);
    }
}
