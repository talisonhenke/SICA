@extends('layouts.main')

@section('content')
<style>
    .cart-container {
        background-color: #f8f9fa;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .cart-table {
        background: #fff;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .cart-table thead {
        background-color: #007bff;
        color: white;
    }

    .cart-table th, .cart-table td {
        vertical-align: middle;
        text-align: center;
    }

    .cart-item-image {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .cart-summary {
        background-color: #fff;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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

</style>

<div class="container my-5">
    <div class="cart-container">
        <h2 class="fw-bold mb-4 text-primary">
            🛍️ Meu Carrinho
        </h2>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(count($cart) > 0)
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
                    <tbody>
                        @foreach($cart as $id => $item)
                            <tr>
                                <td class="fw-semibold">{{ $item['name'] }}</td>
                                <td>
                                    <img src="{{ asset('images/products/' . $id . '/' . basename($item['image'])) }}" class="cart-item-image" alt="{{ $item['name'] }}">
                                </td>
                                <td class="text-success fw-semibold">
                                    R$ {{ number_format($item['price'], 2, ',', '.') }}
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">

                                        {{-- Botão de diminuir --}}
                                        <form action="{{ route('cart.update', $id) }}" method="POST" class="me-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="action" value="decrement">
                                            <button class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-dash-lg"></i>
                                            </button>
                                        </form>

                                        <span class="px-2 fw-bold">{{ $item['quantity'] }}</span>

                                        {{-- Botão de aumentar --}}
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

                                <td class="fw-semibold text-primary">
                                    R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}
                                </td>
                                <td>
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
                        @foreach($cart as $id => $item)
                            <input type="hidden" name="items[{{ $id }}][product_id]" value="{{ $id }}">
                            <input type="hidden" name="items[{{ $id }}][name]" value="{{ $item['name'] }}">
                            <input type="hidden" name="items[{{ $id }}][price]" value="{{ $item['price'] }}">
                            <input type="hidden" name="items[{{ $id }}][quantity]" value="{{ $item['quantity'] }}">
                        @endforeach

                        <input type="hidden" name="total" value="{{ $total }}">

                        {{-- Estes dois serão preenchidos no modal --}}
                        <input type="hidden" name="payment_method" id="payment_method_input">
                        <input type="hidden" name="address_id" id="address_id_input">

                        <a href="{{ route('cart.clear') }}" class="btn btn-outline-danger me-2">
                            <i class="bi bi-x-circle"></i> Esvaziar
                        </a>

                        <button type="button" id="btnCheckout" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#checkoutModal">
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
                <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left"></i> <span class="link-text">Voltar para os produtos</span>
                </a>
            </div>
        @endif
    </div>
</div>

@endsection
