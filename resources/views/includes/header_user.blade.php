<div class="navbarDiv shadow-sm">
    <nav class="navbar navbar-expand-lg px-3">
        <a class="navbar-brand" href="/">
            <img src="{{ asset('images/logo_sica.png') }}" alt="SICA Logo" class="me-2">
            <span class="fw-bold">S.I.C.A</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContentUser">
            <i class="bi bi-list menu-icon"></i>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContentUser">
            <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
                <li class="nav-item"><a class="nav-link" href="/">Página Inicial</a></li>
                <li class="nav-item"><a class="nav-link" href="/plants_list">Lista de Plantas</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('topics.index') }}">Tópicos</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Produtos</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}">Meus Pedidos</a></li>
                
                {{-- Carrinho e tema --}}
                @include('includes.partials.cart_theme_user')

                {{-- Usuário logado / logout --}}
                @include('includes.partials.user_dropdown')
            </ul>
        </div>
    </nav>
</div>
