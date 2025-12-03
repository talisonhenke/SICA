<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $currentStatus = $request->get('status', 'pending');

        $orders = Order::with('user')
            ->when($currentStatus, function($q, $status) {
                return $q->where('status', $status);
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

        $stats = [
            'pending'   => Order::where('status', 'pending')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'shipped'   => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'canceled'  => Order::where('status', 'canceled')->count(),
            'total'     => Order::count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats', 'currentStatus'));
    }


    public function show($id)
    {
        $order = Order::with('user', 'items.product')->findOrFail($id);

        $address = $order->order_address;

        return view('admin.orders.show', compact('order', 'address'));
    }


    /**
     * ================================
     *  ALTERAÇÃO DE STATUS
     * ================================
     */

    // 1. Marcar como pago → preparing
    public function markPaid($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'pending') {
            return back()->with('msg', 'Este pedido não pode ser marcado como pago.');
        }

        $order->status = 'preparing';
        $order->save();

        return back()->with('msg', 'Pedido marcado como pago e movido para Preparando.');
    }


    // 2. Enviar pedido → shipped
    public function ship($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'preparing') {
            return back()->with('msg', 'O pedido só pode ser enviado se estiver em preparação.');
        }

        $order->status = 'shipped';
        $order->save();

        return back()->with('msg', 'Pedido marcado como Enviado.');
    }

    // 3. Pedido entregue pedido → delivered
    public function deliver($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'shipped') {
            return back()->with('msg', 'A entrega só pode ser confirmada após o envio.');
        }

        $order->status = 'delivered';
        $order->save();

        return back()->with('msg', 'Pedido marcado como Entregue.');
    }

    // 4. Cancelar pedido → canceled
    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        if (in_array($order->status, ['delivered', 'canceled'])) {
            return back()->with('msg', 'Este pedido já foi finalizado e não pode ser cancelado.');
        }

        $order->status = 'canceled';
        $order->save();

        return back()->with('msg', 'Pedido cancelado com sucesso.');
    }
}
