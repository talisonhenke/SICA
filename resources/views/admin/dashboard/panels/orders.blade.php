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
    }

    .btn-filter .badge {
        font-size: 0.65rem;
        padding: 3px 6px;
    }

    .btn-filter-pending {
        border-color: var(--color-warning);
        color: #000;
    }

    .btn-filter-pending:hover,
    .btn-filter-pending.active {
        background: var(--color-warning);
        color: #000 !important;
    }

    .btn-filter-preparing {
        border-color: var(--color-primary-dark);
        color: #000;
    }

    .btn-filter-preparing:hover,
    .btn-filter-preparing.active {
        background: var(--color-primary-dark);
        color: #fff !important;
    }

    .btn-filter-shipped {
        border-color: var(--color-info);
        color: #000;
    }

    .btn-filter-shipped:hover,
    .btn-filter-shipped.active {
        background: var(--color-info);
        color: #000 !important;
    }

    .btn-filter-delivered {
        border-color: var(--color-success);
        color: #000;
    }

    .btn-filter-delivered:hover,
    .btn-filter-delivered.active {
        background: var(--color-success);
        color: #000;
    }

    .btn-filter-canceled {
        border-color: var(--color-danger);
        color: #000;
    }

    .btn-filter-canceled:hover,
    .btn-filter-canceled.active {
        background: var(--color-danger);
        color: #fff !important;
    }
</style>

<h4 class="fw-bold mb-3">
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
<div class="mb-3">
    <div class="btn-group flex-wrap">
        @foreach ($filters as $value => $data)
            <a href="{{ route('admin.ajax.dashboard', ['status' => $value]) }}"
               class="btn btn-sm {{ $data['btn_class'] }} @if ($currentStatus === $value) active @endif">
                {{ $data['label'] }}
                <span class="badge ms-2">
                    {{ $orderStats[$value] ?? 0 }}
                </span>
            </a>
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
