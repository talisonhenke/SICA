<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\TopicComment;
use App\Models\PlantComment;
use App\Models\Tag;
use App\Models\User;
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
            'pending' => Order::where('status', 'pending')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'canceled' => Order::where('status', 'canceled')->count(),
            'total' => Order::count(),
        ];

        $currentStatus = $request->get('status', 'pending');

        $orders = Order::with('user')
            ->when($currentStatus, function ($q) use ($currentStatus) {
                $q->where('status', $currentStatus);
            })
            ->orderByRaw(
                "
        CASE
            WHEN status = 'pending' THEN 0
            WHEN status = 'preparing' THEN 1
            WHEN status = 'shipped' THEN 2
            WHEN status = 'delivered' THEN 3
            WHEN status = 'canceled' THEN 4
            ELSE 5
        END
    ",
            )
            ->orderBy('created_at', 'desc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | MODERAÇÃO (CONTADOR + RECENTES)
        |--------------------------------------------------------------------------
        */
        $topicComments = TopicComment::where('moderated', 0)->where(fn($q) => $q->where('toxicity_level', '>=', 0.1)->orWhere('reported', 1))->count();

        $plantComments = PlantComment::where('moderated', 0)->where(fn($q) => $q->where('toxicity_level', '>=', 0.1)->orWhere('reported', 1))->count();

        $moderationCount = $topicComments + $plantComments;

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

        /*
        |--------------------------------------------------------------------------
        | NOTIFICAÇÕES GERAIS (MENU)
        |--------------------------------------------------------------------------
        */
        $adminNotifications = $orderStats['pending'] + $moderationCount;

        return view('admin.dashboard.index', compact(
            'orderStats',
            'orders',
            'currentStatus',
            'moderationCount',
            'tagsCount',
            'recentTags',
            'usersCount',
            'recentUsers',
            'adminNotifications'
        ));

    }
}
