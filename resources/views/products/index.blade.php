@extends('layouts.main')

@section('content')
<style>
    .product-card {
        position: relative;
        border: 1px solid var(--color-border);
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        transition: all 0.2s ease-in-out;
        background-color: var(--color-surface-secondary);
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 5px 10px rgba(0,0,0,0.15);
    }

    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background-color: var(--color-surface-primary);
        border-bottom: 1px solid var(--color-border);
    }

    /* Switch de status */
    .status-switch {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 44px;
        height: 24px;
        background-color: var(--color-border);
        border-radius: 12px;
        cursor: pointer;
        transition: background-color 0.3s;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
    }

    .status-switch::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        background-color: var(--color-surface-primary);
        border-radius: 50%;
        transition: transform 0.3s;
    }

    .status-switch.active {
        background-color: var(--color-success);
    }

    .status-switch.active::after {
        transform: translateX(20px);
    }

    .status-label {
        position: absolute;
        top: 38px;
        right: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--color-text);
        background: var(--color-surface-primary);
        padding: 2px 6px;
        border-radius: 8px;
        border: 1px solid var(--color-border);
    }

    .product-body {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        height: 100%;
    }

    .product-name {
        font-weight: 700;
        font-size: 1.4rem;
        color: var(--color-text);
        margin-bottom: 0.5rem;
        text-align: center;
    }

    .product-price {
        font-weight: 600;
        color: var(--color-success);
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        text-align: center;
    }

    .product-stock {
        color: var(--color-text);
        opacity: 0.8;
        font-size: 1rem;
        margin-bottom: 1rem;
        text-align: center;
    }

    .product-main-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    /* Botão visualizar */
    .btn-view {
        flex: 1;
        background-color: var(--color-primary);
        color: var(--color-menu-text);
        border: none;
        transition: background 0.2s;
    }

    .btn-view:hover {
        background-color: var(--color-primary-dark);
        color: var(--color-menu-text);
    }

    /* Botão carrinho */
    .btn-cart {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--color-success);
        color: #fff;
        border: none;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: background 0.2s;
    }

    .btn-cart:hover {
        background-color: #157347;
    }

    .product-admin-actions {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
    }

    .product-admin-actions .btn {
        padding: 0.25rem 0.6rem;
        font-size: 0.9rem;
    }
</style>


<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold primaryTitles">Produtos</h2>
        @if(Auth::check() && Auth::user()->user_lvl === 'admin')
            <a href="{{ route('products.create') }}" class="btn secondaryButton">+ Adicionar Produto</a>
        @endif
    </div>

    <div class="row g-4">
        @foreach ($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card">
                    {{-- Switch de status (só admins veem) --}}
                    @if(Auth::check() && Auth::user()->user_lvl === 'admin')
                        <div class="status-switch {{ $product->status ? 'active' : '' }}" onclick="toggleStatus({{ $product->id }})"></div>
                        <div class="status-label">{{ $product->status ? 'Ativo' : 'Inativo' }}</div>
                    @endif

                    {{-- Imagem --}}
                    <img src="{{ asset('images/products/' . $product->id . '/' . basename($product->image)) }}" 
                         alt="{{ $product->name }}" 
                         class="product-image">

                    {{-- Corpo --}}
                    <div class="product-body">
                        <h5 class="product-name">{{ $product->name }}</h5>
                        <p class="product-price">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        <p class="product-stock">Estoque: {{ $product->stock }}</p>

                        {{-- Ações principais --}}
                        <div class="product-main-actions">
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-primary">Ver Produto</a>
                            <button class="btn btn-sm btn-success add-to-cart-btn" data-id="{{ $product->id }}">
                                <i class="bi bi-cart"></i>
                            </button>

                        </div>

                        {{-- Ações administrativas --}}
                        @if(Auth::check() && Auth::user()->user_lvl === 'admin')
                            <div class="product-admin-actions">
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary btn-sm">Editar</a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
function toggleStatus(id) {
    fetch(`/products/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(() => location.reload())
    .catch(error => console.error('Erro ao alterar status:', error));
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.add-to-cart-btn');

    buttons.forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.id;

            fetch(`/cart/add/${productId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Mostra uma notificação de sucesso
                const alertBox = document.createElement('div');
                alertBox.className = 'alert alert-success alert-dismissible fade show position-fixed bottom-0 end-0 m-3';
                alertBox.style.zIndex = '1050';
                alertBox.innerHTML = `
                    <strong>✔</strong> ${data.message || 'Produto adicionado ao carrinho!'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(alertBox);

                // Remove o alerta depois de 3 segundos
                setTimeout(() => {
                    alertBox.classList.remove('show');
                    alertBox.addEventListener('transitionend', () => alertBox.remove());
                }, 3000);
            })
            .catch(error => console.error('Erro ao adicionar ao carrinho:', error));
        });
    });
});
</script>

@endsection
