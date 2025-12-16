@extends('layouts.main')

@section('content')
    <style>
        .product-container {
            max-width: 1100px;
            margin: 3rem auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: start;
            background-color: var(--color-surface-secondary);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            width: 100%;
            height: 420px;
            object-fit: contain;
            border-radius: 0.75rem;
            background-color: var(--color-surface-primary);
            border: 1px solid var(--color-border);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .product-details {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .product-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 0.5rem;
        }

        .product-plant {
            font-size: 1.1rem;
            color: var(--color-text);
            opacity: 0.8;
            margin-bottom: 0.75rem;
        }

        .product-description {
            color: var(--color-text);
            font-size: 1.05rem;
            line-height: 1.6;
            text-align: justify;
        }

        .product-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--color-success);
            margin-top: 1rem;
        }

        .product-stock {
            color: var(--color-text);
            opacity: 0.8;
            font-size: 1rem;
        }

        .add-to-cart-form {
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .quantity-input {
            width: 80px;
            text-align: center;
            border: 1px solid var(--color-border);
            border-radius: 0.5rem;
            padding: 0.4rem;
            font-size: 1rem;
            background-color: var(--color-input-bg);
            color: var(--color-input-text);
        }

        .btn-add-cart {
            background-color: var(--color-menu-bg);
            color: var(--color-menu-text);
            border: none;
            border-radius: 0.5rem;
            padding: 0.7rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
        }

        .btn-add-cart:hover {
            background-color: var(--color-secondary);
            color: var(--color-surface-primary);
        }

        .back-button {
            display: inline-block;
            margin-top: 2rem;
            text-decoration: none;
            background-color: var(--color-menu-bg);
            color: var(--color-menu-text);
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .back-button:hover {
            background-color: var(--color-secondary);
            color: var(--color-surface-primary);
        }

        @media (max-width: 900px) {
            .product-container {
                grid-template-columns: 1fr;
            }

            .product-image {
                height: 320px;
            }
        }
    </style>

    <div class="container mt-4">
        {{-- Mensagem de sucesso com script automático --}}
        @if (session('msg'))
            <div id="success-alert" class="alert alert-primary alert-dismissible fade show auto-close" role="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <strong>Mensagem:</strong> {{ session('msg') }}
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const alert = document.getElementById('success-alert');
                    if (alert) {
                        // Fecha automaticamente após 4 segundos
                        setTimeout(() => {
                            const bsAlert = new bootstrap.Alert(alert);
                            bsAlert.close();
                        }, 4000);
                    }
                });
            </script>
        @endif
    </div>

    <div class="product-container">
        {{-- Imagem principal --}}
        <img src="{{ asset('images/products/' . $product->id . '/' . basename($product->image)) }}"
            alt="{{ $product->name }}" class="product-image">

        {{-- Informações do produto --}}
        <div class="product-details">
            <h1 class="product-title">{{ $product->name }}</h1>
            <p class="product-plant">🌿 Planta relacionada:
                <strong>{{ $product->plant->popular_name ?? 'Não informada' }}</strong></p>
            <p class="product-description">{{ $product->description }}</p>

            <p class="product-price">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
            <p class="product-stock">Estoque disponível: {{ $product->stock }}</p>

            {{-- Adicionar ao carrinho --}}
            <div class="add-to-cart-form">
                <button type="button" class="btn-add-cart" onclick="addToCart({{ $product->id }})">
                    🛒 Adicionar ao Carrinho
                </button>

                <a href="{{ route('cart.index') }}" class="btn-add-cart" style="background-color: var(--color-secondary);">
                    Ver Carrinho
                </a>
        </div>


        <a href="{{ route('products.index') }}" class="back-button">← Voltar aos Produtos</a>
    </div>
    </div>

    <script>
        function addToCart(productId) {
            fetch(`/cart/add/${productId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Erro ao adicionar');
                    showToast('Produto adicionado ao carrinho com sucesso!');
                })
                .catch(() => {
                    showToast('Erro ao adicionar produto.', true);
                });
        }

        function showToast(message, error = false) {
            const toast = document.createElement('div');

            toast.className = `alert ${error ? 'alert-danger' : 'alert-success'} position-fixed`;
            toast.style.bottom = '0px';
            toast.style.right = '20px';
            toast.style.zIndex = 9999;
            toast.innerText = message;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    </script>
@endsection
