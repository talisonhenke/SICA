@extends('layouts.main')

@section('content')
    <div class="container py-4">

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
                'pending' => 'Pagamento em análise',
                'preparing' => 'Preparando',
                'shipped' => 'A caminho',
                'delivered' => 'Entregue',
                'canceled' => 'Cancelado',
            ];
        @endphp

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold primaryTitles mb-0">
                Pedido #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
            </h2>

            <a href="{{ route('orders.index') }}" class="btn btn-outline-light">
                Voltar aos meus pedidos
            </a>
        </div>

        {{-- STATUS + INFORMAÇÕES --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                Informações do Pedido
            </div>

            <div class="card-body">

                <p><strong>Status:</strong>
                    <span class="badge bg-{{ $statusColor }}">
                        {{ $statusTranslations[$order->status] ?? $order->status }}
                    </span>
                </p>

                <p><strong>Valor total:</strong>
                    R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                </p>

                <p><strong>Realizado em:</strong>
                    {{ $order->created_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}

                </p>

                <p><strong>Atualizado em:</strong>
                    {{ $order->updated_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}

                </p>

            </div>
        </div>

        {{-- SE STATUS FOR PENDENTE, MOSTRAR BOTÃO PARA VER PIX --}}
        @if ($order->status === 'pending')
            <div class="alert alert-warning d-flex justify-content-between align-items-center">
                <div>
                    <strong>Aguardando confirmação de pagamento.</strong><br>
                    Caso você ainda não tenha pago, clique no botão para gerar novamente o código PIX.
                </div>

                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#pixModal">
                    💳 Ver código PIX
                </button>
            </div>
        @endif


        {{-- ITENS DO PEDIDO --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                Itens do Pedido
            </div>

            <div class="card-body">

                @if ($order->items->isEmpty())
                    <p class="text-muted">Nenhum item encontrado neste pedido.</p>
                @else
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="text-center" style="width: 120px;">Qtd</th>
                                <th class="text-end" style="width: 150px;">Valor unit.</th>
                                <th class="text-end" style="width: 150px;">Subtotal</th>
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
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="text-end">
                                    R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                @endif

            </div>
        </div>


        {{-- ENDEREÇO + MAPA --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                Endereço de Entrega
            </div>

            <div class="card-body">

                @if ($address)
                    <p class="mb-2">
                        <strong>Endereço:</strong>
                        {{ $address['street'] ?? '' }},
                        {{ $address['number'] ?? '' }} —
                        {{ $address['district'] ?? '' }} —
                        {{ $address['city'] ?? '' }}
                    </p>

                    <div id="orderMap" style="width: 100%; height: 350px; border-radius: 10px; overflow: hidden;"></div>

                    <script>
                        const orderLat = {{ $address['latitude'] ?? 'null' }};
                        const orderLng = {{ $address['longitude'] ?? 'null' }};
                    </script>
                @else
                    <p class="text-muted">Endereço não disponível.</p>
                @endif

            </div>
        </div>

    </div>

    {{-- MODAL PIX --}}
    <div class="modal fade" id="pixModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Pagamento via PIX</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <p class="text-muted text-center">
                        Utilize o código abaixo para realizar o pagamento.<br>
                        <strong class="text-danger">Se você já pagou, não pague novamente.</strong>
                    </p>

                    {{-- QR CODE --}}
                    <div class="text-center mb-3">
                        {!! QrCode::size(240)->generate($order->order_pix) !!}
                    </div>

                    {{-- CÓDIGO PIX --}}
                    <label class="fw-bold">Código PIX:</label>
                    <div class="p-2 bg-light border rounded" id="pixCodeModal"
                        style="font-family: monospace; max-height: 140px; overflow:auto;">
                        {{ $order->order_pix }}
                    </div>

                    <button class="btn btn-primary w-100 mt-2" onclick="copyPixModal()">
                        📋 Copiar código PIX
                    </button>

                </div>

            </div>
        </div>
    </div>

    <script>
        function copyPixModal() {
            navigator.clipboard.writeText(
                document.getElementById("pixCodeModal").innerText
            ).then(() => {
                alert("Código PIX copiado!");
            });
        }
    </script>


    {{-- GOOGLE MAPS --}}
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            if (!orderLat || !orderLng) {
                return;
            }

            const position = {
                lat: orderLat,
                lng: orderLng
            };

            const map = new google.maps.Map(document.getElementById("orderMap"), {
                center: position,
                zoom: 17,
                streetViewControl: false,
                fullscreenControl: false,
                mapTypeControl: false,
                draggable: false
            });

            new google.maps.Marker({
                position: position,
                map: map
            });
        });
    </script>
@endsection
