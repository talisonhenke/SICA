@extends('layouts.main')

@section('content')
    <style>
        .cart-container {
            background-color: #f8f9fa;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .cart-table {
            background: #fff;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .cart-table thead {
            background-color: #007bff;
            color: white;
        }

        .cart-table th,
        .cart-table td {
            vertical-align: middle;
            text-align: center;
        }

        .cart-item-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .cart-summary {
            background-color: #fff;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .cart-summary h4 {
            font-weight: 700;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: #fff;
        }

        .cart-empty {
            text-align: center;
            padding: 4rem 0;
            color: #6c757d;
        }

        .cart-empty i {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 1rem;
        }

        .cart-empty p {
            font-size: 1.1rem;
        }

        .btn-back-products {
            padding: 0.6rem 1.4rem;
            font-weight: 500;
        }

        /* ===== Ajustes para mobile ===== */
    @media (max-width: 768px) {

        .cart-table table,
        .cart-table thead,
        .cart-table tbody,
        .cart-table th,
        .cart-table td,
        .cart-table tr {
            display: block;
            width: 100%;
        }

        .btn-outline-danger {
            margin-bottom: 15px;
        }
        .cart-table thead {
            display: none; /* esconde cabeçalho */
        }

        .cart-table tr {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #ddd;
            padding-bottom: 1rem;
        }

        .cart-table td {
            text-align: left;
            padding: 0.5rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-table td::before {
            content: attr(data-label);
            font-weight: 600;
            flex-basis: 50%;
            color: #6c757d;
        }

        .cart-item-image {
            width: 60px;
            height: 60px;
        }

        .cart-summary {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .cart-summary h4 {
            margin-bottom: 1rem;
        }

        .cart-summary div {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }
    }
    </style>

    <div class="container my-5">
        <div class="cart-container">
            <h2 class="fw-bold mb-4 text-primary">
                🛍️ Meu Carrinho
            </h2>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (count($cart) > 0)
                <div class="cart-table table-responsive mb-4">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Imagem</th>
                                <th>Preço</th>
                                <th>Quantidade</th>
                                <th>Total</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        {{-- Ajuste nas colunas para mobile --}}
<tbody>
@foreach ($cart as $id => $item)
    <tr>
        <td data-label="Produto" class="fw-semibold">{{ $item['name'] }}</td>
        <td data-label="Imagem">
            <img src="{{ asset('images/products/' . $id . '/' . basename($item['image'])) }}"
                class="cart-item-image" alt="{{ $item['name'] }}">
        </td>
        <td data-label="Preço" class="text-success fw-semibold">
            R$ {{ number_format($item['price'], 2, ',', '.') }}
        </td>
        <td data-label="Quantidade">
            <div class="d-flex justify-content-center align-items-center">
                <form action="{{ route('cart.update', $id) }}" method="POST" class="me-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="decrement">
                    <button class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-dash-lg"></i>
                    </button>
                </form>

                <span class="px-2 fw-bold">{{ $item['quantity'] }}</span>

                <form action="{{ route('cart.update', $id) }}" method="POST" class="ms-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="increment">
                    <button class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </form>
            </div>
        </td>
        <td data-label="Total" class="fw-semibold text-primary">
            R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}
        </td>
        <td data-label="Ações">
            <a href="{{ route('cart.remove', $id) }}" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash"></i> Remover
            </a>
        </td>
    </tr>
@endforeach
</tbody>
                    </table>
                </div>

                <div class="cart-summary mt-4 d-flex justify-content-between align-items-center">
                    <h4>Total: <span class="text-success">R$ {{ number_format($total, 2, ',', '.') }}</span></h4>
                    <div>
                        <form action="{{ route('orders.store') }}" method="POST" id="checkoutForm">
                            @csrf

                            {{-- ENVIO DOS ITENS --}}
                            @foreach ($cart as $id => $item)
                                <input type="hidden" name="items[{{ $id }}][product_id]"
                                    value="{{ $id }}">
                                <input type="hidden" name="items[{{ $id }}][name]" value="{{ $item['name'] }}">
                                <input type="hidden" name="items[{{ $id }}][price]"
                                    value="{{ $item['price'] }}">
                                <input type="hidden" name="items[{{ $id }}][quantity]"
                                    value="{{ $item['quantity'] }}">
                            @endforeach

                            <input type="hidden" name="total" value="{{ $total }}">

                            {{-- Estes dois serão preenchidos no modal --}}
                            <input type="hidden" name="payment_method" id="payment_method_input">
                            <input type="hidden" name="address_id" id="address_id_input">
                            <input type="hidden" name="select_address" id="select_address">
                            <input type="hidden" name="order_address_json" id="order_address_json">


                            <a href="{{ route('cart.clear') }}" class="btn btn-outline-danger me-2">
                                <i class="bi bi-x-circle"></i> Esvaziar
                            </a>

                            <button type="button" id="btnCheckout" class="btn btn-success btn-lg" data-bs-toggle="modal"
                                data-bs-target="#checkoutModal">
                                <i class="bi bi-credit-card"></i> Finalizar Compra
                            </button>

                            {{-- MODAL --}}
                            @include('checkout.modal_order_confirm')

                        </form>


                    </div>
                </div>
            @else
                <div class="cart-empty">
                    <i class="bi bi-cart-x"></i>
                    <p class="empty-cart-text">Seu carrinho está vazio no momento.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary mt-3 btn-back-products">
                        Voltar para os produtos
                    </a>


                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const select = document.getElementById("addressSelect");
            const hidden = document.getElementById("order_address_json");

            // Copia o valor inicial do SELECTED OPTION para o campo hidden
            hidden.value = select.value;
            console.log('Endereço selecionado JSON:', hidden.value);
        });
    </script>

    <script>
        //TODO: Ajustar lógica para salvar só os dados necessários do endereço
        document.getElementById('addressSelect').addEventListener('change', function() {
            document.getElementById('select_address').value = this.value;
            document.getElementById('order_address_json').value = this.value;
            console.log('Endereço selecionado ID:', this.value);
            console.log('Endereço selecionado JSON:', document.getElementById('order_address_json').value);
        });

        document.getElementById("btnMainConfirm").addEventListener("click", function() {

            const selectedId = document.getElementById("addressSelect").value;

            // SE USUÁRIO ESCOLHEU ENDEREÇO EXISTENTE
            if (!document.getElementById('order_address_json').value) {

                // precisamos criar o JSON automaticamente
                fetch('/addresses/get/' + selectedId)
                    .then(res => res.json())
                    .then(address => {
                        document.getElementById('order_address_json').value = JSON.stringify(address);
                        document.getElementById('selected_address_id').value = selectedId;

                        document.getElementById("realCheckoutForm").submit();
                    });

            } else {
                // já existe o JSON (porque usuário criou um novo)
                document.getElementById("realCheckoutForm").submit();
            }
        });
    </script>


@endsection
