<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteReview;

class SiteReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        SiteReview::create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Avaliação registrada!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = SiteReview::where('user_id', auth()->id())->firstOrFail();

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Avaliação atualizada!');
    }
    public function adminIndex()
    {
        $reviews = SiteReview::with('user')->paginate(10);

        // Média geral
        $average = SiteReview::avg('rating');

        // Arredondamento estilo 4.0, 4.5, 4.0
        $rounded = 0;

        if ($average !== null) {
            $int = floor($average);
            $decimal = $average - $int;

            if ($decimal < 0.5) {
                $rounded = $int; // Ex: 4.2 => 4
            } elseif ($decimal == 0.5) {
                $rounded = $int + 0.5; // Ex: 4.5
            } else {
                $rounded = $int + 0.5; // Ex: 4.8 => 4.5
            }
        }

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'average' => $average,
            'rounded' => $rounded,
        ]);
    }
}
