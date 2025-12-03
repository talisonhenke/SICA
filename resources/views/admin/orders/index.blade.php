@extends('layouts.main')

@section('content')
    <style>
        /* BASE – estilo aplicado a todos os botões de filtro */
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

        /* Estado ativo */
        .btn-filter.active {
            color: #fff !important;
        }

        /* Badge interno */
        .btn-filter .badge {
            font-size: 0.65rem;
            padding: 3px 6px;
        }

        /* ============================== */
        /*  CORES POR STATUS (CUSTOM)     */
        /* ============================== */

        /* PENDING */
        .btn-filter-pending {
            border-color: var(--color-warning);
            color: #000;
            background: transparent;
        }

        .btn-filter-pending:hover,
        .btn-filter-pending.active {
            background: var(--color-warning);
            color: #000 !important;
        }

        .btn-filter-pending .badge {
            background: var(--color-warning);
            color: #000;
        }

        /* PREPARING */
        .btn-filter-preparing {
            border-color: var(--color-primary-dark);
            color: #000;
        }

        .btn-filter-preparing:hover,
        .btn-filter-preparing.active {
            background: var(--color-primary-dark);
            color: #fff !important;
        }

        .btn-filter-preparing .badge {
            background: var(--color-primary-dark);
            color: #fff;
        }

        /* SHIPPED */
        .btn-filter-shipped {
            border-color: var(--color-info);
            color: #000;
        }

        .btn-filter-shipped:hover,
        .btn-filter-shipped.active {
            background: var(--color-info);
            color: #000 !important;
        }

        .btn-filter-shipped .badge {
            background: var(--color-info);
            color: #000;
        }

        /* DELIVERED */
        .btn-filter-delivered {
            border-color: var(--color-success);
            color: #000;
        }

        .btn-filter-delivered:hover,
        .btn-filter-delivered.active {
            background: var(--color-success);
            color: #000;
        }

        .btn-filter-delivered .badge {
            background: var(--color-success);
            color: #000;
        }

        /* CANCELED */
        .btn-filter-canceled {
            border-color: var(--color-danger);
            color: #000;
        }

        .btn-filter-canceled:hover,
        .btn-filter-canceled.active {
            background: var(--color-danger);
            color: #fff !important;
        }

        .btn-filter-canceled .badge {
            background: var(--color-danger);
            color: #fff;
        }
    </style>

    <div class="container py-4">

        <h2 class="mb-4 fw-bold primaryTitles">
            Pedidos

            @if ($stats['pending'] > 0)
                <span class="badge bg-danger ms-2" style="font-size: 0.7rem;">
                    {{ $stats['pending'] }} pendente(s)
                </span>
            @endif
        </h2>

        {{-- =================================== --}}
        {{-- FILTROS DE STATUS                   --}}
        {{-- =================================== --}}

        @php
            $filters = [
                'pending' => [
                    'label' => 'Pendentes',
                    'btn_class' => 'btn-filter-pending',
                    'badge_class' => 'badge-filter-pending',
                ],
                'preparing' => [
                    'label' => 'Preparando',
                    'btn_class' => 'btn-filter-preparing',
                    'badge_class' => 'badge-filter-preparing',
                ],
                'shipped' => [
                    'label' => 'Enviados',
                    'btn_class' => 'btn-filter-shipped',
                    'badge_class' => 'badge-filter-shipped',
                ],
                'delivered' => [
                    'label' => 'Entregues',
                    'btn_class' => 'btn-filter-delivered',
                    'badge_class' => 'badge-filter-delivered',
                ],
                'canceled' => [
                    'label' => 'Cancelados',
                    'btn_class' => 'btn-filter-canceled',
                    'badge_class' => 'badge-filter-canceled',
                ],
            ];
        @endphp

        {{-- =================================== --}}
        {{-- TABELA DE PEDIDOS                   --}}
        {{-- =================================== --}}

        <div class="card shadow-sm border-0">
            <div class="card shadow-sm border-0">

                {{-- ========================== --}}
                {{-- FAIXA DE FILTROS VISUAL   --}}
                {{-- ========================== --}}
                <div class="filters-wrapper p-3 rounded shadow-sm bg-white">
                    <div class="btn-group">
                        @foreach ($filters as $value => $data)
                            <a href="{{ route('admin.orders.index', ['status' => $value]) }}"
                                class="btn btn-sm {{ $data['btn_class'] }} @if ($currentStatus === $value) active @endif">
                                {{ $data['label'] }}
                                <span class="badge {{ $data['badge_class'] }} ms-2">
                                    {{ $stats[$value] ?? 0 }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>
            {{-- ========================== --}}
            {{-- TABELA DE PEDIDOS          --}}
            {{-- ========================== --}}

            <div class="card-header bg-white fw-bold">
                @if (isset($filters[$currentStatus]))
                    Pedidos — {{ $filters[$currentStatus]['label'] }}
                @else
                    Pedidos Recentes
                @endif
            </div>

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
                                        'shipped' => 'Enviado',
                                        'delivered' => 'Entregue',
                                        'canceled' => 'Cancelado',
                                    ];
                                @endphp

                                <tr>
                                    <td>#{{ $order->id }}</td>

                                    <td>{{ $order->user->name ?? 'Usuário não encontrado' }}</td>

                                    <td>
                                        <span class="badge bg-{{ $statusColor }}">
                                            {{ strtoupper($statusTranslations[$order->status] ?? $order->status) }}
                                        </span>
                                    </td>

                                    <td class="fw-bold">
                                        R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                    </td>

                                    <td>{{ $order->created_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</td>

                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Ver
                                        </a>
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

    </div>
@endsection
