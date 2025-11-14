@if(Auth::check())
<nav class="mobile-bottom-nav d-lg-none">
    <a href="/" class="nav-item">
        <i class="bi bi-house-door"></i>
        <span>Início</span>
    </a>
    <a href="{{ route('products.index') }}" class="nav-item">
        <i class="bi bi-bag"></i>
        <span>Produtos</span>
    </a>
    <a href="{{ route('topics.index') }}" class="nav-item">
        <i class="bi bi-chat-dots"></i>
        <span>Tópicos</span>
    </a>
    <a href="{{ route('plants.index') }}" class="nav-item">
        <i class="bi bi-flower1"></i>
        <span>Plantas</span>
    </a>
    <a href="#" id="openQrModal" class="nav-item">
        <i class="bi bi-qr-code-scan"></i>
        <span>QR Code</span>
    </a>
</nav>
@endif
