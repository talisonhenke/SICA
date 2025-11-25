<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    /**
     * Exibe os detalhes de um pedido específico.
     */
    public function show($id)
    {
        // Carregar o pedido com o usuário associado
        $order = Order::with('user')->findOrFail($id);

        // Decodificar o endereço JSON (caso exista)
        $address = $order->order_address;

        return view('admin.orders.show', compact('order', 'address'));
    }
}
