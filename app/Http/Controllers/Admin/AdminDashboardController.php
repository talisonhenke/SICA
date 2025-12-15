<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


use App\Models\Order;
use App\Models\TopicComment;
use App\Models\PlantComment;
use App\Models\Tag;
use App\Models\User;
use App\Models\SiteReview;
class AdminDashboardController extends Controller
{
    public function index(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | ORDERS (RESUMO + LISTA RECENTE)
    |--------------------------------------------------------------------------
    */
    $orderStats = [
        'pending'    => Order::where('status', 'pending')->count(),
        'preparing'  => Order::where('status', 'preparing')->count(),
        'shipped'    => Order::where('status', 'shipped')->count(),
        'delivered'  => Order::where('status', 'delivered')->count(),
        'canceled'   => Order::where('status', 'canceled')->count(),
        'total'      => Order::count(),
    ];

    $currentStatus = $request->get('status', 'pending');

    $orders = Order::with('user')
        ->when($currentStatus, function ($q) use ($currentStatus) {
            $q->where('status', $currentStatus);
        })
        ->orderByRaw("
            CASE
                WHEN status = 'pending' THEN 0
                WHEN status = 'preparing' THEN 1
                WHEN status = 'shipped' THEN 2
                WHEN status = 'delivered' THEN 3
                WHEN status = 'canceled' THEN 4
                ELSE 5
            END
        ")
        ->orderBy('created_at', 'desc')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | MODERAÇÃO (CONTADOR)
    |--------------------------------------------------------------------------
    */
    $topicCount = TopicComment::where('moderated', 0)
        ->where(fn ($q) => $q->where('toxicity_level', '>=', 0.1)->orWhere('reported', 1))
        ->count();

    $plantCount = PlantComment::where('moderated', 0)
        ->where(fn ($q) => $q->where('toxicity_level', '>=', 0.1)->orWhere('reported', 1))
        ->count();

    $moderationCount = $topicCount + $plantCount;

    /*
    |--------------------------------------------------------------------------
    | TAGS
    |--------------------------------------------------------------------------
    */
    $tagsCount = Tag::count();
    $recentTags = Tag::orderBy('created_at', 'desc')->limit(5)->get();

    /*
    |--------------------------------------------------------------------------
    | USERS
    |--------------------------------------------------------------------------
    */
    $usersCount = User::count();
    $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();

    // NOVAS AVALIAÇÕES 

    $newReviewsCount = SiteReview::where('new_reviews', 'unread')->count();

    /*
    |--------------------------------------------------------------------------
    | NOTIFICAÇÕES (MENU)
    |--------------------------------------------------------------------------
    */
    $adminNotifications = $orderStats['pending'] + $moderationCount;

    return view(
        'admin.dashboard.index',
        compact(
            'orderStats',
            'orders',
            'currentStatus',
            'moderationCount',
            // 'comments',
            // 'filter',
            'tagsCount',
            'recentTags',
            'usersCount',
            'recentUsers',
            'adminNotifications',
            'newReviewsCount'
        )
    );
}

     public function moderationPanelAjax(Request $request)
{
    $filter = $request->get('filter', 'all');

    $topicQuery = TopicComment::with('user')
        ->where('moderated', 0)
        ->where(fn ($q) =>
            $q->where('toxicity_level', '>=', 0.1)
              ->orWhere('reported', 1)
        );

    $plantQuery = PlantComment::with('user')
        ->where('moderated', 0)
        ->where(fn ($q) =>
            $q->where('toxicity_level', '>=', 0.1)
              ->orWhere('reported', 1)
        );

    switch ($filter) {
        case 'suspect':
            $topicQuery->where('toxicity_level', '>=', 0.5);
            $plantQuery->where('toxicity_level', '>=', 0.5);
            break;

        case 'high':
            $topicQuery->where('toxicity_level', '>=', 0.7);
            $plantQuery->where('toxicity_level', '>=', 0.7);
            break;

        case 'reported':
            $topicQuery->where('reported', 1);
            $plantQuery->where('reported', 1);
            break;
    }

    $topicComments = $topicQuery->get()->each(fn ($c) => $c->comment_type = 'topic');
    $plantComments = $plantQuery->get()->each(fn ($c) => $c->comment_type = 'plant');

    $comments = $topicComments
        ->merge($plantComments)
        ->sortByDesc('created_at')
        ->values();

    return view('admin.dashboard.panels.moderation-table', compact('comments', 'filter'));
}

public function notifications()
{
    $orderPending = Order::where('status', 'pending')->count();

    $topicCount = TopicComment::where('moderated', 0)
        ->where(fn ($q) => $q->where('toxicity_level', '>=', 0.1)->orWhere('reported', 1))
        ->count();

    $plantCount = PlantComment::where('moderated', 0)
        ->where(fn ($q) => $q->where('toxicity_level', '>=', 0.1)->orWhere('reported', 1))
        ->count();

    $moderationCount = $topicCount + $plantCount;

    $newReviewsCount = SiteReview::where('new_reviews', 'unread')->count();

    $totalNotifications = $orderPending + $moderationCount + $newReviewsCount;

    return response()->json([
        'total' => $totalNotifications
    ]);
}

}
