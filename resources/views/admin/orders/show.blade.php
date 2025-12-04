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
                'pending' => 'Pendente',
                'preparing' => 'Preparando',
                'shipped' => 'Enviado',
                'delivered' => 'Entregue',
                'canceled' => 'Cancelado',
            ];
        @endphp

        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary mb-3">
            ← Voltar aos pedidos
        </a>

        <h2 class="fw-bold mb-4 primaryTitles">
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

                <p><strong>Status:</strong>
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

                <!-- @if ($order->order_pix)
    <p><strong>Código Pix:</strong> {{ $order->order_pix }}</p>
    @endif -->

            </div>
        </div>

        {{-- ============================== --}}
        {{-- ITENS DO PEDIDO                --}}
        {{-- ============================== --}}
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
                                <th class="text-end" style="width: 150px;">Preço unitário</th>
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


        {{-- ENDEREÇO DO PEDIDO --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">
                Endereço do Pedido
            </div>

            <div class="card-body">

                @if ($address)
                    {{-- Exibir endereço simples --}}
                    <p class="mb-2">
                        <strong>Endereço:</strong>
                        {{ $address['street'] ?? '' }},
                        {{ $address['number'] ?? '' }} —
                        {{ $address['district'] ?? '' }} —
                        {{ $address['city'] ?? '' }}
                    </p>

                    {{-- Div do mapa --}}
                    <div id="orderMap" style="width: 100%; height: 350px; border-radius: 10px; overflow: hidden;">
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

            {{-- PEDIDO PENDENTE (cliente ainda não pagou) --}}
            @if ($order->status === 'pending')
                <form action="{{ route('admin.orders.markPaid', $order->id) }}" id="formMarkPaid" method="POST"
                    class="d-inline">
                    @csrf
                    <button type="submit" id="btnMarkPaid" class="btn btn-success">
                        Marcar como Pago
                    </button>
                </form>

                <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-dark">
                        Cancelar Pedido
                    </button>
                </form>
            @endif


            {{-- PAGAMENTO CONFIRMADO — preparando --}}
            @if ($order->status === 'preparing')
                <form id="formShip" action="{{ route('admin.orders.ship', $order->id) }}" id="formShip" method="POST"
                    class="d-inline">
                    @csrf
                    <button id="btnShip" type="submit" class="btn btn-primary me-2">
                        Enviar Pedido
                    </button>
                </form>

                <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-dark">
                        Cancelar Pedido
                    </button>
                </form>
            @endif


            {{-- PEDIDO ENVIADO — aguardando entrega --}}
            @if ($order->status === 'shipped')
                <span class="badge bg-info p-2 d-block mb-3">
                    Aguardando entrega (até 3 horas)
                </span>

                {{-- BOTÃO CONFIRMAR ENTREGA (delivered) --}}
                <form id="formDelivered" action="{{ route('admin.orders.deliver', $order->id) }}" method="POST"
                    class="d-inline">
                    @csrf
                    <button id="btnDelivered" type="submit" class="btn btn-success me-2">
                        Confirmar Entrega
                    </button>
                </form>

                {{-- BOTÃO CANCELAR --}}
                <form id="formCancel" action="{{ route('admin.orders.cancel', $order->id) }}" method="POST"
                    class="d-inline">
                    @csrf
                    <button id="btnCancel" type="submit" class="btn btn-dark">
                        Cancelar Pedido
                    </button>
                </form>
            @endif



            {{-- ENTREGUE --}}
            @if ($order->status === 'delivered')
                <span class="badge bg-success p-2">
                    Pedido entregue
                </span>
            @endif


            {{-- CANCELADO --}}
            @if ($order->status === 'canceled')
                <span class="badge bg-dark p-2 text-white">
                    Pedido cancelado
                </span>
            @endif

        </div>

        <div class="modal fade" id="whatsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Enviar mensagem ao cliente</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label for="msg">Mensagem:</label>
                        <textarea id="msg" class="form-control" rows="8">{{ session('whatsapp_message') }}</textarea>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" onclick="copyMsg()">Copiar Mensagem</button>

                        <a class="btn btn-success" target="_blank" href="https://wa.me/{{ session('whatsapp_number') }}">
                            Abrir WhatsApp
                        </a>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            if (!orderLat || !orderLng) {
                console.warn("Latitude/Longitude ausentes.");
                return;
            }

            const position = {
                lat: orderLat,
                lng: orderLng
            };

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

    <script>
        function copyMsg() {
            const el = document.getElementById('msg');
            el.select();
            navigator.clipboard.writeText(el.value);
            alert('Mensagem copiada!');
        }
    </script>


    @if (session('whatsapp_message'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('whatsModal'));
                modal.show();
            });
        </script>
    @endif


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* ============================================
               1. BOTÃO MARCAR COMO PAGO (POST)
            ============================================ */
            const btnMarkPaid = document.getElementById("btnMarkPaid");
            const formMarkPaid = document.getElementById("formMarkPaid");

            if (btnMarkPaid && formMarkPaid) {
                btnMarkPaid.addEventListener("click", (e) => {
                    e.preventDefault();

                    btnMarkPaid.disabled = true;
                    btnMarkPaid.innerHTML = "Processando...";

                    setTimeout(() => {
                        formMarkPaid.submit();
                    }, 1000);
                });
            }


            /* ============================================
               2. BOTÃO ENVIAR PEDIDO (ship — POST)
            ============================================ */
            const btnShip = document.getElementById('btnShip');
            const formShip = document.getElementById('formShip');

            if (btnShip && formShip) {
                btnShip.addEventListener('click', function(e) {
                    e.preventDefault();

                    const message =
                        "Atenção!\n\n" +
                        "Marque esta opção APENAS se o pedido JÁ está nas mãos do entregador.\n" +
                        "Tem certeza que o entregador já coletou o pedido?";

                    if (confirm(message)) {
                        btnShip.disabled = true;
                        btnShip.innerHTML = "Enviando...";

                        setTimeout(() => {
                            formShip.submit(); // CORREÇÃO — antes estava errado
                        }, 1000);
                    }
                });
            }


            /* ============================================
               3. BOTÃO CANCELAR PEDIDO (POST + ALERTA)
            ============================================ */
            const btnCancel = document.getElementById("btnCancel");
            const formCancel = document.getElementById("formCancel");

            if (btnCancel && formCancel) {
                btnCancel.addEventListener("click", function(e) {
                    e.preventDefault();

                    const message =
                        "Tem certeza que deseja CANCELAR este pedido?\n\n" +
                        "Esta ação não poderá ser desfeita.";

                    if (confirm(message)) {
                        btnCancel.disabled = true;
                        btnCancel.innerHTML = "Cancelando...";

                        setTimeout(() => {
                            formCancel.submit();
                        }, 1000);
                    }
                });
            }

        });
    </script>
@endsection
