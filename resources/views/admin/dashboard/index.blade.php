@extends('layouts.main')

@section('content')
<div class="container py-4">

    <h2 class="mb-4 fw-bold">
        Dashboard Administrativo

        {{-- Notificação de pedidos pendentes --}}
        @if($stats['pending'] > 0)
            <span class="badge bg-danger ms-2" style="font-size: 0.7rem;">
                {{ $stats['pending'] }} pendente(s)
            </span>
        @endif
    </h2>

    {{-- ======================== --}}
    {{-- CARDS DE ESTATÍSTICAS   --}}
    {{-- ======================== --}}
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="fw-bold text-secondary">Pendentes</h6>
                    <h3 class="fw-bold text-danger">{{ $stats['pending'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="fw-bold text-secondary">Pagos</h6>
                    <h3 class="fw-bold text-success">{{ $stats['paid'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="fw-bold text-secondary">Cancelados</h6>
                    <h3 class="fw-bold text-dark">{{ $stats['canceled'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="fw-bold text-secondary">Total</h6>
                    <h3 class="fw-bold text-primary">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>

    </div>

    {{-- ======================== --}}
    {{-- TABELA DE PEDIDOS       --}}
    {{-- ======================== --}}

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold">
            Pedidos Recentes
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
                                // Badge de status
                                $statusColor = [
                                    'pending'  => 'warning',
                                    'paid'     => 'success',
                                    'canceled' => 'dark',
                                ][$order->status] ?? 'secondary';
                            @endphp

                            <tr>
                                <td>#{{ $order->id }}</td>

                                <td>
                                    {{ $order->user->name ?? 'Usuário não encontrado' }}
                                </td>

                                <td>
                                    <span class="badge bg-{{ $statusColor }}">
                                        {{ strtoupper($order->status) }}
                                    </span>
                                </td>

                                <td class="fw-bold">
                                    R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                </td>

                                <td>
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>

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
