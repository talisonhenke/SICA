{{-- =============================== --}}
{{-- PAINEL DE PEDIDOS – DASHBOARD --}}
{{-- =============================== --}}

<style>
    /* Mantive seus estilos exatamente como estavam */
    .btn-filter {
        border: 1px solid transparent;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        transition: 0.2s ease;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .btn-filter.active {
        color: #fff !important;
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.08);

    }
    .btn-filter .badge {
        font-size: 0.65rem;
    }

    .btn-filter-pending, .btn-filter-pending .badge {
        border-color: var(--color-warning);
        color: #000;
    }

    .btn-filter-pending:hover,
    .btn-filter-pending.active,
    .btn-filter-pending:hover .badge,
    .btn-filter-pending.active .badge {
        background: var(--color-warning);
        color: #000 !important;
    }

    .btn-filter-preparing, .btn-filter-preparing .badge {
        border-color: var(--color-primary-dark);
        color: #000;
    }

    .btn-filter-preparing:hover,
    .btn-filter-preparing.active,
    .btn-filter-preparing:hover .badge,
    .btn-filter-preparing.active .badge {
        background: var(--color-primary-dark);
        color: #fff !important;
    }

    .btn-filter-shipped, .btn-filter-shipped .badge {
        border-color: var(--color-info);
        color: #000;
    }

    .btn-filter-shipped:hover,
    .btn-filter-shipped.active,
    .btn-filter-shipped:hover .badge,
    .btn-filter-shipped:active .badge  {
        background: var(--color-info);
        color: #000 !important;
    }

    .btn-filter-delivered, .btn-filter-delivered .badge {
        border-color: var(--color-success);
        color: #000;
    }

    .btn-filter-delivered:hover,
    .btn-filter-delivered.active,
    .btn-filter-delivered:hover .badge,
    .btn-filter-delivered.active .badge {
        background: var(--color-success);
        color: #000;
    }

    .btn-filter-canceled, .btn-filter-canceled .badge{
        border-color: var(--color-danger);
        color: #000;
    }

    .btn-filter-canceled:hover,
    .btn-filter-canceled.active,
    .btn-filter-canceled:hover .badge,
    .btn-filter-canceled.active .badge {
        background: var(--color-danger);
        color: #fff !important;
    }
        .orders-filters {
        background: var(--color-surface-secondary);
        border: 1px solid rgba(0, 0, 0, 0.05);
        padding: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }

    .orders-filters .btn-group {
        gap: 6px;
    }
</style>


<h4 class="fw-bold ms-2 my-3">
    Pedidos
    @if ($orderStats['pending'] > 0)
        <span class="badge bg-danger ms-2" style="font-size: 0.7rem;">
            {{ $orderStats['pending'] }} pendente(s)
        </span>
    @endif
</h4>

@php
    $filters = [
        'pending' => ['label' => 'Pendentes', 'btn_class' => 'btn-filter-pending'],
        'preparing' => ['label' => 'Preparando', 'btn_class' => 'btn-filter-preparing'],
        'shipped' => ['label' => 'Enviados', 'btn_class' => 'btn-filter-shipped'],
        'delivered' => ['label' => 'Entregues', 'btn_class' => 'btn-filter-delivered'],
        'canceled' => ['label' => 'Cancelados', 'btn_class' => 'btn-filter-canceled'],
    ];
@endphp

{{-- ========================== --}}
{{-- FILTROS --}}
{{-- ========================== --}}
<div class="mb-4 orders-filters">
    <div class="btn-group flex-wrap">
        @foreach ($filters as $value => $data)
            <button
    type="button"
    class="btn btn-sm btn-filter {{ $data['btn_class'] }} {{ $currentStatus === $value ? 'active' : '' }}"
    data-status="{{ $value }}"
>
    {{ $data['label'] }}
    <span class="badge ms-2">
        {{ $orderStats[$value] ?? 0 }}
    </span>
</button>

        @endforeach
    </div>
</div>


{{-- ========================== --}}
{{-- TABELA --}}
{{-- ========================== --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($orders as $order)
                        @php
                            $statusMap = [
                                'pending' => ['warning', 'Pendente'],
                                'preparing' => ['info', 'Preparando'],
                                'shipped' => ['primary', 'Em entrega'],
                                'delivered' => ['success', 'Entregue'],
                                'canceled' => ['dark', 'Cancelado'],
                            ];
                        @endphp

                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->user->name ?? 'Usuário não encontrado' }}</td>
                            <td>
                                <span class="badge bg-{{ $statusMap[$order->status][0] ?? 'secondary' }}">
                                    {{ strtoupper($statusMap[$order->status][1] ?? $order->status) }}
                                </span>
                            </td>
                            <td class="fw-bold">
                                R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary js-open-order"
                                    data-url="{{ route('admin.orders.ajax.modal', $order->id) }}">
                                    Ver
                                </button>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Nenhum pedido encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


