{{-- Carrinho desktop --}}
<li class="nav-item d-none d-lg-block">
    <a class="nav-link position-relative d-flex align-items-center gap-1" href="{{ route('cart.index') }}">
        <i class="bi bi-cart3"></i>
        <span>Carrinho</span>
        @if(session('cart') && count(session('cart')) > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ count(session('cart')) }}
            </span>
        @endif
    </a>
</li>

{{-- Carrinho mobile --}}
<li class="nav-item d-lg-none">
    <a class="nav-link position-relative d-flex align-items-center justify-content-between" href="{{ route('cart.index') }}">
        <span class="spanItems">Carrinho</span>
        <i class="bi bi-cart3"></i>
        @if(session('cart') && count(session('cart')) > 0)
            <span class="badge bg-danger ms-2">
                {{ count(session('cart')) }}
            </span>
        @endif
    </a>
</li>

{{-- Botão de tema desktop --}}
<li class="nav-item d-none d-lg-flex align-items-center ms-2">
    <button id="themeToggleBtn" class="theme-btn">
        <i class="bi bi-sun-fill" id="themeIcon"></i>
    </button>
</li>

{{-- Botão de tema mobile --}}
<li class="nav-item d-lg-none">
    <button id="themeToggleBtnMobile" class="theme-btn-mobile w-100 text-start">
        <span class="me-2">Tema</span>
        <i class="bi bi-sun-fill" id="themeIconMobile"></i>
    </button>
</li>
