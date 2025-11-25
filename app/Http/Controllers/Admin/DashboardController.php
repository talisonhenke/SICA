<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        // Carrega pedidos + usuário relacionado
        // Prioriza pendentes, depois ordena por data de criação
        $orders = Order::with('user')
            ->orderByRaw("
                CASE 
                    WHEN status = 'pending' THEN 0
                    ELSE 1
                END
            ")
            ->orderBy('created_at', 'desc')
            ->get();

        // Contadores para o dashboard (bem úteis)
        $stats = [
            'pending'   => Order::where('status', 'pending')->count(),
            'paid'      => Order::where('status', 'paid')->count(),
            'canceled'  => Order::where('status', 'canceled')->count(),
            'total'     => Order::count(),
        ];

        return view('admin.dashboard.index', compact('orders', 'stats'));
    }

}
