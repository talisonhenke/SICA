@extends('layouts.main')

@section('content')
    <style>
        /* Reaproveitando o estilo dos status */
        .badge-status-warning {
            background: var(--color-warning);
            color: #000;
        }

        .badge-status-info {
            background: var(--color-info);
            color: #000;
        }

        .badge-status-primary {
            background: var(--color-primary-dark);
            color: #fff;
        }

        .badge-status-success {
            background: var(--color-success);
            color: #000;
        }

        .badge-status-danger {
            background: var(--color-danger);
            color: #fff;
        }

        .badge-status-dark {
            background: #333;
            color: #fff;
        }

        /* Área dos filtros */
        .user-filters .btn {
            border-radius: 6px;
            font-weight: 600;
        }
    </style>


    <div class="container py-4">

        <h2 class="fw-bold mb-4 primaryTitles">Meus Pedidos</h2>


        {{-- =================================================== --}}
        {{-- LISTA DE PEDIDOS DO CLIENTE                        --}}
        {{-- =================================================== --}}

        <div class="card shadow-sm border-0">

            {{-- =================================================== --}}
            {{-- FILTROS SIMPLES PARA O CLIENTE                     --}}
            {{-- =================================================== --}}
            <div class="card shadow-sm border-0">
                <div class="user-filters p-3">
                    <div class="btn-group">

                        <a href="{{ route('orders.index', ['filter' => 'open']) }}"
                            class="btn btn-sm btn-outline-primary @if ($filter === 'open') active @endif">
                            Abertos
                            <span class="badge bg-primary ms-1">{{ $stats['open'] }}</span>
                        </a>

                        <a href="{{ route('orders.index', ['filter' => 'delivered']) }}"
                            class="btn btn-sm btn-outline-success @if ($filter === 'delivered') active @endif">
                            Entregues
                            <span class="badge bg-success ms-1">{{ $stats['delivered'] }}</span>
                        </a>

                        <a href="{{ route('orders.index', ['filter' => 'canceled']) }}"
                            class="btn btn-sm btn-outline-danger @if ($filter === 'canceled') active @endif">
                            Cancelados
                            <span class="badge bg-danger ms-1">{{ $stats['canceled'] }}</span>
                        </a>

                    </div>
                </div>
            </div>

            <div class="card-header bg-white fw-bold">
                @if ($filter === 'open')
                    Pedidos em Aberto
                @elseif($filter === 'delivered')
                    Pedidos Entregues
                @elseif($filter === 'canceled')
                    Pedidos Cancelados
                @else
                    Todos os Pedidos
                @endif
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        <tbody>

                            @php
                                $statusLabels = [
                                    'pending' => ['Pendente', 'warning'],
                                    'preparing' => ['Preparando', 'info'],
                                    'shipped' => ['Enviado', 'primary'],
                                    'delivered' => ['Entregue', 'success'],
                                    'canceled' => ['Cancelado', 'danger'],
                                ];
                            @endphp

                            @forelse($orders as $order)
                                @php
                                    $label = $statusLabels[$order->status][0] ?? $order->status;
                                    $color = $statusLabels[$order->status][1] ?? 'secondary';
                                @endphp

                                <tr>
                                    <td>#{{ $order->id }}</td>

                                    <td>
                                        <span class="badge badge-status-{{ $color }}">
                                            {{ $label }}
                                        </span>
                                    </td>

                                    <td class="fw-bold">
                                        R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                    </td>

                                    <td>{{ $order->created_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}</td>

                                    <td>
                                        <a href="{{ route('orders.show', $order->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Ver detalhes
                                        </a>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Você ainda não possui pedidos.
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
