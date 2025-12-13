@php
    $statusColor =
        [
            'pending' => 'warning',
            'preparing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'canceled' => 'dark',
        ][$order->status] ?? 'secondary';

    $statusTranslations = [
        'pending' => 'Pendente',
        'preparing' => 'Preparando',
        'shipped' => 'Em entrega',
        'delivered' => 'Entregue',
        'canceled' => 'Cancelado',
    ];

    function formatPhone($phone)
    {
        if (!$phone) {
            return null;
        }
        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) !== 11) {
            return $phone;
        }

        return sprintf(
            '(%s) %s %s-%s',
            substr($digits, 0, 2),
            substr($digits, 2, 1),
            substr($digits, 3, 4),
            substr($digits, 7, 4),
        );
    }
@endphp

{{-- TÍTULO + AÇÃO --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <h4 class="fw-bold mb-0">
        Pedido #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
    </h4>

    @if (in_array($order->status, ['preparing', 'shipped']) && !empty($whatsappMessage) && !empty($whatsappNumber))
        <button class="btn btn-success btn-sm js-open-whatsapp" data-message="{{ $whatsappMessage }}"
            data-phone="{{ $whatsappNumber }}">
            <i class="bi bi-whatsapp"></i> Enviar status do pedido
        </button>
    @endif

</div>


{{-- ================= CLIENTE ================= --}}
<div class="card mb-3">
    <div class="card-header fw-bold">Cliente</div>
    <div class="card-body">
        <p><strong>Nome:</strong> {{ $order->user->name ?? 'Não informado' }}</p>
        <p><strong>Email:</strong> {{ $order->user->email ?? 'Não informado' }}</p>
        <p><strong>Telefone:</strong> {{ formatPhone($order->user->phone_number) ?? 'Não informado' }}</p>
    </div>
</div>

{{-- ================= PEDIDO ================= --}}
<div class="card mb-3">
    <div class="card-header fw-bold">Informações do Pedido</div>
    <div class="card-body">
        <p>
            <strong>Status:</strong>
            <span class="badge bg-{{ $statusColor }}">
                {{ strtoupper($statusTranslations[$order->status] ?? $order->status) }}
            </span>
        </p>

        <p><strong>Valor total:</strong>
            R$ {{ number_format($order->total_amount, 2, ',', '.') }}
        </p>

        <p><strong>Data do pedido:</strong>
            {{ $order->created_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
        </p>

        <p><strong>Última atualização:</strong>
            {{ $order->updated_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
        </p>
    </div>
</div>

{{-- ================= ITENS ================= --}}
<div class="card mb-3">
    <div class="card-header fw-bold">Itens do Pedido</div>
    <div class="card-body">

        @if ($order->items->isEmpty())
            <p class="text-muted">Nenhum item encontrado.</p>
        @else
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th class="text-center" style="width:120px">Qtd</th>
                        <th class="text-end" style="width:150px">Preço</th>
                        <th class="text-end" style="width:150px">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'Produto removido' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">
                                R$ {{ number_format($item->price, 2, ',', '.') }}
                            </td>
                            <td class="text-end">
                                R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th class="text-end">
                            R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        @endif

    </div>
</div>

{{-- ================= ENDEREÇO ================= --}}
<div class="card mb-3">
    <div class="card-header fw-bold">Endereço do Pedido</div>
    <div class="card-body">

        @if ($address)
            <p>
                <strong>Endereço:</strong>
                {{ $address['street'] ?? '' }},
                {{ $address['number'] ?? '' }} —
                {{ $address['district'] ?? '' }} —
                {{ $address['city'] ?? '' }}
            </p>

            <div id="orderMap" data-lat="{{ $address['latitude'] ?? '' }}"
                data-lng="{{ $address['longitude'] ?? '' }}"
                style="width:100%; height:300px; border-radius:10px; overflow:hidden;">
            </div>
        @else
            <p class="text-muted">Endereço não disponível.</p>
        @endif

    </div>
</div>

{{-- ================= AÇÕES ================= --}}
<div class="text-end mt-3">

    @if ($order->status === 'pending')
        <button class="btn btn-success js-mark-paid" data-url="{{ route('admin.orders.ajax.markPaid', $order->id) }}">
            Marcar como pago
        </button>

        <button class="btn btn-outline-danger ms-2 js-cancel-order"
            data-url="{{ route('admin.orders.ajax.cancel', $order->id) }}">
            Cancelar pedido
        </button>
    @endif


    @if ($order->status === 'preparing')
        <button class="btn btn-primary js-mark-shipped" data-url="{{ route('admin.orders.ajax.ship', $order) }}">
            Enviar pedido
        </button>
    @endif


    @if ($order->status === 'shipped')
        <form action="{{ route('admin.orders.deliver', $order->id) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success">Confirmar Entrega</button>
        </form>
    @endif

</div>
