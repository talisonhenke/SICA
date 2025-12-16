@extends('layouts.main')

@section('content')
    <style>
        .pix-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.08);
        }

        .pix-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #28a745;
            text-align: center;
        }

        .pix-qrcode {
            text-align: center;
            margin: 25px 0;
        }

        .pix-box {
            background: #f7f7f7;
            padding: 15px;
            border-radius: 12px;
            font-family: monospace;
            font-size: 0.95rem;
            color: #333;
            max-height: 150px;
            overflow: auto;
            border: 1px solid #ddd;
        }

        .copy-btn {
            width: 100%;
            margin-top: 12px;
        }

        .store-info {
            margin-top: 25px;
            padding: 15px;
            background: #f1f1f1;
            border-radius: 12px;
        }

        .btn-orders-link {
            background-color: var(--color-primary);;
            /* border: 2px solid var(--color-primary); */
            color: var(--color-surface-primary);
            font-weight: bold;
            font-weight: 500;
            padding: 0.5rem 1.4rem;
            border-radius: 10px;
            /* pill elegante */
            transition: all 0.2s ease-in-out;
        }

        .btn-orders-link:hover {
            background-color: var(--color-primary-dark);
            color: var(--color-surface-primary);
            text-decoration: none;
        }

        .btn-orders-link:focus {
            box-shadow: 0 0 0 0.15rem rgba(0, 0, 0, 0.08);
        }
    </style>

    <div class="pix-container">

        <h2 class="pix-title">Pagamento via PIX</h2>

        <p class="text-center mt-2">
            Seu pedido foi criado com sucesso!
            Agora finalize o pagamento para que possamos confirmar.
        </p>

        <hr>

        <h5><strong>Pedido #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</strong></h5>

        <p class="mt-3">
            <strong>Valor Total:</strong>
            <span class="text-success fw-bold">
                R$ {{ number_format($order->total_amount, 2, ',', '.') }}
            </span>
        </p>

        <div class="pix-qrcode">
            {!! $qrcodeSvg !!}
        </div>

        <h6 class="mt-3">Copie o código PIX:</h6>

        <div class="pix-box" id="pixCode">
            {{ $order->order_pix }}
        </div>

        <button class="btn btn-primary copy-btn" onclick="copyPix()">
            📋 Copiar código PIX
        </button>

        <div class="store-info mt-4">
            <h6><strong>Horários de Funcionamento</strong></h6>
            <p class="mb-1">🕒 Segunda a Sexta: <strong>09:00 às 18:00</strong></p>
            <p class="mb-1">📅 Sábado: <strong>09:00 às 12:00</strong></p>
            <p class="text-muted">Pagamentos fora do horário podem demorar um pouco para serem confirmados.</p>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('orders.index') }}" class="btn btn-orders-link">
                Ver meus pedidos
            </a>

        </div>


    </div>

    <script>
        function copyPix() {
            let code = document.getElementById("pixCode").innerText;

            navigator.clipboard.writeText(code).then(() => {
                alert("Código PIX copiado!");
            });
        }
    </script>
@endsection
