<div class="modal-header">
    <h5 class="modal-title">
        Pedido #{{ $order->id }}
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

    {{-- CLIENTE --}}
    <p class="mb-2">
        <strong>Cliente:</strong>
        {{ $order->user->name ?? 'Usuário não encontrado' }}
    </p>

    {{-- STATUS --}}
    <p class="mb-3">
        <strong>Status:</strong>
        <span class="badge bg-secondary">
            {{ strtoupper($order->status) }}
        </span>
    </p>

    <hr>

    {{-- ITENS --}}
    <h6 class="fw-bold mb-2">Itens do Pedido</h6>

    <table class="table table-sm">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Qtd</th>
                <th>Preço</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Produto removido' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>
                        R$ {{ number_format($item->price, 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <p class="fw-bold">
        Total:
        R$ {{ number_format($order->total_amount, 2, ',', '.') }}
    </p>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        Fechar
    </button>

    <a href="{{ route('admin.orders.ajax.modal', $order->id) }}"
       class="btn btn-outline-primary">
        Abrir página completa
    </a>
</div>
