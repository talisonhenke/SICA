<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderAdminController extends Controller
{
    public function index(Request $request)
    {
        $currentStatus = $request->get('status', 'pending');

        $orders = Order::with('user')
            ->when($currentStatus, function ($q, $status) {
                return $q->where('status', $status);
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

        $stats = [
            'pending' => Order::where('status', 'pending')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'canceled' => Order::where('status', 'canceled')->count(),
            'total' => Order::count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats', 'currentStatus'));
    }

    public function show($id)
    {
        $order = Order::with('user', 'items.product')->findOrFail($id);

        $address = $order->order_address;

        return view('admin.orders.show', compact('order', 'address'));
    }

    private function buildWhatsappMessage($order)
    {
        $itemsList = $order->items
            ->map(function ($i) {
                return "- {$i->product->name} (x{$i->quantity})";
            })
            ->implode("\n");

        return "Olá {$order->user->name}, tudo bem?\n\n" . "Seu pagamento foi confirmado! 🎉\n" . "Estamos preparando seu pedido #{$order->id}.\n\n" . "Itens:\n{$itemsList}\n\n" . "Valor total: R$ {$order->total}\n" . 'Agradecemos sua compra!';
    }

    /**
     * ================================
     *  ALTERAÇÃO DE STATUS
     * ================================
     */

    // 1. Marcar como pago → preparing
    public function markPaid($id)
    {
        $order = Order::with('user', 'items.product')->findOrFail($id);

        if ($order->status !== 'pending') {
            return back()->with('msg', 'Este pedido não pode ser marcado como pago.');
        }

        $order->status = 'preparing';
        $order->save();

        // Número do cliente (DDD + número sem espaços)
        $phone = preg_replace('/\D/', '', $order->user->phone_number);

        // Mensagem personalizada
        $message = "Olá *{$order->user->name}*, seu pagamento foi confirmado! 🎉\n";
        $message .= "Agora seu pedido está sendo preparado.\n\n";
        $message .= "Resumo do pedido:\n";

        foreach ($order->items as $item) {
            $message .= "- {$item->product->name} (*x{$item->quantity}*)\n";
        }

        $message .= "\nTotal do pedido: *R$ " . number_format($order->total_amount, 2, ',', '.') . "*\n";

        $message .= "\nObrigado por comprar conosco! 😊";

        return back()->with('msg', 'Pedido marcado como pago e movido para Preparando.')->with('whatsapp_message', $message)->with('whatsapp_number', $phone);
    }

    // 2. Enviar pedido → shipped
    public function ship($id)
    {
        $order = Order::with('user', 'items.product')->findOrFail($id);

        if ($order->status !== 'preparing') {
            return back()->with('msg', 'O pedido só pode ser enviado se estiver em preparação.');
        }

        $order->status = 'shipped';
        $order->save();

        // Número do cliente (DDD + número sem espaços)
        $phone = preg_replace('/\D/', '', $order->user->phone_number);

        // Mensagem personalizada de envio
        $message = "Olá *{$order->user->name}*, seu pedido foi enviado! 🚚💨\n";
        $message .= "Agora ele está a caminho da entrega.\n\n";
        $message .= "Resumo do pedido:\n";

        foreach ($order->items as $item) {
            $message .= "- {$item->product->name} (*x{$item->quantity}*)\n";
        }

        $message .= "\nTotal do pedido: *R$ " . number_format($order->total_amount, 2, ',', '.') . "*\n";

        $message .= "\nQualquer dúvida, estou à disposição! 😊";

        return back()->with('msg', 'Pedido marcado como Enviado.')->with('whatsapp_message', $message)->with('whatsapp_number', $phone);
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

    public function orderModal($id)
    {
        try {
            Log::info('[OrderModal] Iniciando carregamento', ['order_id' => $id]);

            // Carrega pedido com usuário, itens e produtos
            $order = Order::with(['user', 'items.product'])->findOrFail($id);

            // Carrega o endereço do pedido (como antes)
            $address = $order->order_address;

            Log::info('[OrderModal] Pedido carregado com sucesso', [
                'order_id' => $order->id,
                'items_count' => $order->items->count(),
            ]);

            // Passa order e address para a view do modal
            return view('admin.orders.ajax.modal', compact('order', 'address'));
        } catch (\Throwable $e) {
            Log::error('[OrderModal] ERRO AO CARREGAR PEDIDO', [
                'order_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json(
                [
                    'message' => 'Erro ao carregar pedido.',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
