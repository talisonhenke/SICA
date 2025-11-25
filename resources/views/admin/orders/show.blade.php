@extends('layouts.main')

@section('content')
<div class="container py-4">

    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary mb-3">
        ← Voltar ao Dashboard
    </a>

    <h2 class="fw-bold mb-4">
        Detalhes do Pedido #{{ $order->id }}
    </h2>

    {{-- ============================== --}}
    {{-- INFORMAÇÕES DO CLIENTE         --}}
    {{-- ============================== --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">
            Cliente
        </div>

        <div class="card-body">
            <p class="mb-1"><strong>Nome:</strong> {{ $order->user->name ?? 'Não informado' }}</p>
            <p class="mb-1"><strong>Email:</strong> {{ $order->user->email ?? 'Não informado' }}</p>
            <!-- <p class="mb-0"><strong>ID do usuário:</strong> {{ $order->user_id }}</p> -->
        </div>
    </div>

    {{-- ============================== --}}
    {{-- INFORMAÇÕES DO PEDIDO          --}}
    {{-- ============================== --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">
            Informações do Pedido
        </div>

        <div class="card-body">

            @php
                $statusColor = [
                    'pending' => 'warning',
                    'paid' => 'success',
                    'canceled' => 'dark'
                ][$order->status] ?? 'secondary';
            @endphp

            <p><strong>Status:</strong>
                <span class="badge bg-{{ $statusColor }}">
                    {{ strtoupper($order->status) }}
                </span>
            </p>

            <p><strong>Valor total:</strong>
                R$ {{ number_format($order->total_amount, 2, ',', '.') }}
            </p>

            <p><strong>Data do pedido:</strong>
                {{ $order->created_at->format('d/m/Y H:i') }}
            </p>

            <p><strong>Última atualização:</strong>
                {{ $order->updated_at->format('d/m/Y H:i') }}
            </p>

            <!-- @if($order->order_pix)
                <p><strong>Código Pix:</strong> {{ $order->order_pix }}</p>
            @endif -->

        </div>
    </div>

    {{-- ENDEREÇO DO PEDIDO --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">
            Endereço do Pedido
        </div>

        <div class="card-body">

            @if($address)
                {{-- Exibir endereço simples --}}
                <p class="mb-2">
                    <strong>Endereço:</strong>
                    {{ $address['street'] ?? '' }}, 
                    {{ $address['number'] ?? '' }} — 
                    {{ $address['district'] ?? '' }} — 
                    {{ $address['city'] ?? '' }}
                </p>

                {{-- Div do mapa --}}
                <div id="orderMap"
                    style="width: 100%; height: 350px; border-radius: 10px; overflow: hidden;">
                </div>

                {{-- Variáveis para JavaScript --}}
                <script>
                    const orderLat = {{ $address['latitude'] ?? 'null' }};
                    const orderLng = {{ $address['longitude'] ?? 'null' }};
                </script>

            @else
                <p class="text-muted">Endereço não disponível.</p>
            @endif

        </div>
    </div>


    {{-- ============================== --}}
    {{-- AÇÕES DO ADMIN                 --}}
    {{-- ============================== --}}
    <div class="text-end">

        @if($order->status === 'pending')
            <a href="#" class="btn btn-success me-2">Marcar como Pago</a>
            <a href="#" class="btn btn-danger">Cancelar Pedido</a>
        @endif

        @if($order->status === 'paid')
            <a href="#" class="btn btn-dark">Cancelar Pedido</a>
        @endif

    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    if (!orderLat || !orderLng) {
        console.warn("Latitude/Longitude ausentes.");
        return;
    }

    const position = { lat: orderLat, lng: orderLng };

    // Criar mapa
    const map = new google.maps.Map(document.getElementById("orderMap"), {
        center: position,
        zoom: 17,
        streetViewControl: false,
        fullscreenControl: false,
        mapTypeControl: false,
        draggable: false
    });

    // Criar marcador fixo
    new google.maps.Marker({
        position: position,
        map: map,
        draggable: false
    });
});
</script>

@endsection
