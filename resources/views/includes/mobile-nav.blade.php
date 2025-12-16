<style>
    /* Mobile menu */
    /* Mobile bottom navigation */
    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 65px;
        background: var(--color-bottom-nav-bg);
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: space-around;
        align-items: center;
        z-index: 1050;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    }

    .mobile-bottom-nav .nav-item {
        flex: 1;
        text-align: center;
        color: var(--color-bottom-nav-text);
        font-size: 12px;
        background: none;
        border: none;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        transition: color 0.3s ease, background-color 0.3s ease;
        font: inherit;
        padding: 0;
    }

    .mobile-bottom-nav .nav-item i {
        font-size: 22px;
        margin-bottom: 2px;
    }

    .mobile-bottom-nav .nav-item:hover,
    .mobile-bottom-nav .nav-item.active {
        background-color: var(--color-accent);
        color: #fff;
    }

    @media (min-width: 992px) {
        .mobile-bottom-nav {
            display: none;
        }
    }

    /* Off-Canvas Mobile Menu */
    .mobile-offcanvas {
        position: fixed;
        top: 0;
        left: -100%;
        width: 70%;
        height: 100%;
        background-color: var(--color-bottom-nav-bg);
        box-shadow: 2px 0 12px rgba(0, 0, 0, 0.2);
        z-index: 1100;
        transition: left 0.3s ease;
        touch-action: pan-y;
        display: flex;
        flex-direction: column;
    }

    .mobile-offcanvas.active {
        left: 0;
    }

    .offcanvas-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .offcanvas-menu {
        list-style: none;
        padding: 0;
        margin: 0;
        flex: 1;
        overflow-y: auto;
    }

    .offcanvas-menu li {
        border-bottom: 1px solid var(--border-color);
    }

    .offcanvas-menu li a {
        display: block;
        position: relative;
        padding: 1rem;
        color: var(--color-bottom-nav-text);
        text-decoration: none;
    }

    .offcanvas-menu li a:hover {
        background-color: var(--color-accent);
        color: #fff;
    }

    .offcanvas-menu li button {
        display: block;
        position: relative;
        padding: 1rem;
        color: var(--color-bottom-nav-text);
        text-decoration: none;
    }

    .offcanvas-menu li button:hover {
        background-color: var(--color-accent);
        color: #fff;
    }
    .close-btn {
        font-size: 1.5rem;
        background: none;
        border: none;
        cursor: pointer;
        color: var(--color-bottom-nav-text);
    }

    /* Badge de notificação */
    .nav-badge {
        position: absolute;
        top: 4px;
        right: 12px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 50%;
        background-color: red;
        color: #fff;
        font-size: 12px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        pointer-events: none;
    }

    .offcanvas-badge {
    display: inline-flex;         /* deixa o badge inline com o texto */
    align-items: center;
    justify-content: center;
    margin-left: 6px;             /* distância mínima do texto */
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 50%;
    background-color: red;
    color: #fff;
    font-size: 12px;
    font-weight: bold;
    line-height: 1;
}

    /* Para o botão “Início” relativo ao ícone */
    #mobileMenuToggle {
        position: relative;
    }
    .theme-btn-mobile-inf {
        background: none;
        border: none;
        padding: 1rem;
        text-align: left;
        color: var(--color-bottom-nav-text);
        font-size: 14px;
        width: 100%;
        cursor: pointer;
    }

    .theme-btn-mobile-inf span {
        flex: 1;
    }

    .theme-btn-mobile-inf i {
        transition: transform 0.3s ease;
    }

    .theme-animated {
        transform: rotate(20deg);
    }
</style>

<nav class="mobile-bottom-nav d-lg-none">
    <!-- Botão Início abre o menu lateral -->
    <button type="button" class="nav-item" id="mobileMenuToggle">
        <i class="bi bi-house-door"></i>
        <span>Início</span>
        @if (session('cart') && count(session('cart')) > 0)
            <span class="nav-badge">{{ count(session('cart')) }}</span>
        @endif
    </button>

    <!-- Links que navegam -->
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
    <button type="button" id="openQrModal" class="nav-item">
        <i class="bi bi-qr-code-scan"></i>
        <span>QR Code</span>
    </button>
</nav>

<div id="mobileOffCanvas" class="mobile-offcanvas">
    <div class="offcanvas-header">
        <h5 class="text-white">Menu</h5>
        <button id="closeOffCanvas" class="close-btn">&times;</button>
    </div>
    <ul class="offcanvas-menu">

        @if (Auth::check() && Auth::user()->hasVerifiedEmail())
            <li><a href="{{ route('orders.index') }}">Meus Pedidos</a></li>

            <li>
                <a href="{{ route('cart.index') }}">
                    Carrinho
                    @if (session('cart') && count(session('cart')) > 0)
                        <span class="offcanvas-badge">{{ count(session('cart')) }}</span>
                    @endif
                </a>
            </li>

        <!-- Itens restantes -->
        <li><a href="{{ route('products.index') }}">Produtos</a></li>
        <li><a href="{{ route('topics.index') }}">Tópicos</a></li>
        <li><a href="{{ route('plants.index') }}">Plantas</a></li>

        <li><a href="{{ route('profile.edit') }}">Editar Perfil</a></li>

        <li>
            <button id="themeToggleBtnMobileInf" class="theme-btn-mobile-inf w-100 d-flex justify-content-between align-items-center">
                <span>Tema</span>
                <i class="bi bi-sun-fill" id="themeIconMobileInf"></i>
            </button>
        </li>

         @else
            <li><a href="{{ Auth::check() ? route('verification.notice') : route('login') }}">Entrar</a></li>
        @endif
        <!-- Logout -->
        @if (Auth::check() && Auth::user()->hasVerifiedEmail())
            <li>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Sair
                </a>
            </li>
        @endif
    </ul>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>



<script>
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileOffCanvas = document.getElementById('mobileOffCanvas');
    const closeOffCanvas = document.getElementById('closeOffCanvas');

    let isOpen = false; // Estado real do menu
    let isAnimating = false; // Trava para impedir múltiplas ações
    let startX = 0;
    let currentX = 0;

    // Função para abrir menu
    function openMenu() {
        if (isAnimating || isOpen) return;
        isAnimating = true;
        mobileOffCanvas.classList.add('active');
    }

    // Função para fechar menu
    function closeMenu() {
        if (isAnimating || !isOpen) return;
        isAnimating = true;
        mobileOffCanvas.classList.remove('active');
    }

    // Atualiza estado ao terminar a transição
    mobileOffCanvas.addEventListener('transitionend', function() {
        isOpen = mobileOffCanvas.classList.contains('active');
        isAnimating = false;
    });

    // Clique no botão para abrir
    mobileMenuToggle.addEventListener('click', openMenu);

    // Clique no X para fechar
    closeOffCanvas.addEventListener('click', closeMenu);

    // Clicar fora do menu para fechar
    document.addEventListener('click', function(e) {
        if (isOpen && !mobileOffCanvas.contains(e.target) && e.target !== mobileMenuToggle) {
            closeMenu();
        }
    });

    // Arrastar para fechar (touch)
    mobileOffCanvas.addEventListener('touchstart', function(e) {
        if (!isOpen) return;
        startX = e.touches[0].clientX;
        currentX = startX;
    });

    mobileOffCanvas.addEventListener('touchmove', function(e) {
        if (!isOpen) return;
        currentX = e.touches[0].clientX;
        let deltaX = currentX - startX;
        if (deltaX < 0) {
            mobileOffCanvas.style.left = Math.max(deltaX, -mobileOffCanvas.offsetWidth) + 'px';
        }
    });

    mobileOffCanvas.addEventListener('touchend', function() {
        if (!isOpen) return;
        let deltaX = currentX - startX;
        mobileOffCanvas.style.left = '';
        if (deltaX < -100) {
            closeMenu();
        }
    });
</script>

<script>
    function applyIconMode(theme) {
        const icons = document.querySelectorAll("#themeIcon, #themeIconMobileInf");

        icons.forEach(icon => {
            if (!icon) return;

            icon.classList.add("theme-animated");

            setTimeout(() => icon.classList.remove("theme-animated"), 300);

            if (theme === "dark") {
                icon.classList.remove("bi-sun-fill");
                icon.classList.add("bi-moon-fill");
            } else {
                icon.classList.remove("bi-moon-fill");
                icon.classList.add("bi-sun-fill");
            }
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        const html = document.documentElement;
        const btnMobile = document.getElementById("themeToggleBtnMobileInf");

        // Ler tema salvo
        let savedTheme = localStorage.getItem("theme") || "light";
        if (savedTheme !== "light" && savedTheme !== "dark") savedTheme = "light";

        // Aplicar tema ao carregar
        html.setAttribute("data-theme", savedTheme);
        applyIconMode(savedTheme);

        // Função de alternância
        function toggleTheme() {
            const current = html.getAttribute("data-theme");
            const newTheme = current === "light" ? "dark" : "light";

            html.setAttribute("data-theme", newTheme);
            localStorage.setItem("theme", newTheme);

            applyIconMode(newTheme);
        }

        if (btnMobile) btnMobile.addEventListener("click", toggleTheme);
    });
</script>
