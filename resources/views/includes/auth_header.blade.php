<div class="navbarDiv shadow-sm">
    <style>
        .navbarDiv {
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar {
            transition: all 0.3s ease-in-out;
            background-color: var(--color-menu-bg) !important;
        }

        /* Alinhamento perfeito do logo */
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            /* espaço entre logo e texto */
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            line-height: 1;
            color: var(--color-menu-text);
        }

        .navbar-brand img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            display: block;
            margin: 0;
            padding: 0;
            border: 0;
            align-self: center;
            vertical-align: middle;
            box-sizing: content-box;
        }

        .navbar-brand span {
            letter-spacing: 1px;
            display: inline-block;
            line-height: 1;
            position: relative;
            top: 1px;
            /* ajuste fino */
            color: var(--color-menu-text);
        }

        .menu-icon {
            color: var(--color-menu-text);
            font-size: 30px;
        }

        .menu-icon:hover {
            color: var(--color-warning);
        }

        /* .themeIcon{
            color: var(--color-menu-text) !important;
        } */

        .spanItems {
            color: var(--color-menu-text) !important;
        }

        /* Botão padrão para tema (desktop) */
        .theme-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 50%;
            transition: background 0.2s ease, transform 0.2s;
            color: var(--color-menu-text);
            font-size: 1.3rem;
        }

        .theme-btn:hover {
            background: var(--color-bg);
            transform: scale(1.1);
        }

        /* Versão mobile */
        .theme-btn-mobile {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 10px 0;
            font-size: 1rem;
            color: var(--color-menu-text);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .theme-btn-mobile:hover {
            background: var(--color-bg);
        }

        .theme-btn-mobile i {
            font-size: 1.3rem;
        }

        /* Animação sutil para trocar ícone */
        @keyframes rotate-icon {
            from {
                transform: rotate(-90deg);
                opacity: 0;
            }

            to {
                transform: rotate(0);
                opacity: 1;
            }
        }

        .theme-animated {
            animation: rotate-icon 0.3s ease;
        }


        .nav-link {
            font-weight: 500;
            transition: color 0.2s, background-color 0.2s;
            border-radius: 0.375rem;
            margin: 0 3px;
            color: var(--color-menu-text) !important;
        }

        .nav-link:hover,
        .nav-link:focus {
            background-color: var(--color-bg);
            color: var(--color-menu-text) !important;
        }

        .dropdown-menu {
            border-radius: 0.5rem;
            overflow: hidden;
            background-color: var(--color-surface-primary);
            color: var(--color-text);
        }

        .dropdown-item {
            color: var(--color-text);
        }

        .dropdown-item:hover {
            background-color: var(--color-accent);
        }

        .btn-outline-light {
            transition: all 0.2s ease-in-out;
            border-color: var(--color-menu-text);
            color: var(--color-menu-text);
        }

        .btn-outline-light:hover {
            background-color: var(--color-surface-primary) !important;
            color: var(--color-text) !important;
        }

        /* Botão do usuário (logado) */
        .nav-link.dropdown-toggle.bg-white {
            background-color: var(--color-surface) !important;
            color: var(--color-primary) !important;
        }

        .nav-link.dropdown-toggle.bg-white:hover {
            background-color: var(--color-herb-light) !important;
        }

        /* Link do painel do administrador */
        .admin-panel-link {
            position: relative;
            display: flex;
            align-items: center;
            /* garante que o badge fique alinhado verticalmente */
            gap: 0.4rem;
            /* espaço entre texto e badge */
        }

        /* Badge exclusivo do menu do administrador */
        .admin-badge {
            background-color: #dc3545;
            /* vermelho, como outros badges de alerta */
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            padding: 0 6px;
        }
    </style>

    <nav class="navbar navbar-expand-lg px-3">
        {{-- Logo (navbar-brand) --}}
        <a class="navbar-brand" href="/">
            <img src="{{ asset('images/logo_sica.png') }}" alt="SICA Logo" class="me-2">
            <span class="fw-bold">S.I.C.A</span>
        </a>

        {{-- Botão do menu mobile --}}
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent">
            {{-- <span class="navbar-toggler-icon"></span> --}}
            <i class="bi bi-list menu-icon"></i>
        </button>

        {{-- Menu --}}
        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">

                {{-- ========================= --}}
                {{-- ADMINISTRADOR --}}
                {{-- ========================= --}}
                @if (Auth::check() && Auth::user()->user_lvl === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.ajax.dashboard') }}" class="nav-link admin-panel-link">
                            Painel do Administrador
                            <span id="admin-notification-badge" class="admin-badge" style="display: none;">0</span>
                        </a>
                    </li>
                @endif



                {{-- ========================= --}}
                {{-- PÁGINA INICIAL (NÃO ADMIN) --}}
                {{-- ========================= --}}
                @if (!Auth::check() || (Auth::check() && Auth::user()->user_lvl !== 'admin'))
                    <li class="nav-item">
                        <a class="nav-link" href="/">Página Inicial</a>
                    </li>
                @endif

                {{-- ========================= --}}
                {{-- CORE DO SISTEMA (TODOS) --}}
                {{-- ========================= --}}
                <li class="nav-item">
                    <a class="nav-link" href="/plants_list">Lista de Plantas</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('topics.index') }}">Tópicos</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('products.index') }}">Produtos</a>
                </li>

            </ul>

        </div>
    </nav>
</div>
