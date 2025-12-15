<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderPanelController extends Controller
{
    public function index(Request $request)
    {
        /*
        |------------------------------------------------------------------
        | STATUS ATUAL (FILTRO)
        |------------------------------------------------------------------
        */
        $currentStatus = $request->get('status', 'pending');

        /*
        |------------------------------------------------------------------
        | RESUMO DOS PEDIDOS (BADGES / MENU)
        |------------------------------------------------------------------
        */
        $orderStats = [
            'pending'    => Order::where('status', 'pending')->count(),
            'preparing'  => Order::where('status', 'preparing')->count(),
            'shipped'    => Order::where('status', 'shipped')->count(),
            'delivered'  => Order::where('status', 'delivered')->count(),
            'canceled'   => Order::where('status', 'canceled')->count(),
            'total'      => Order::count(),
        ];

        /*
        |------------------------------------------------------------------
        | LISTA DE PEDIDOS (SEM PAGINAÇÃO)
        |------------------------------------------------------------------
        */
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
        |------------------------------------------------------------------
        | VIEW DO PAINEL
        |------------------------------------------------------------------
        */
        return view(
            'admin.dashboard.panels.orders',
            compact(
                'orders',
                'orderStats',
                'currentStatus'
            )
        );
    }
}
