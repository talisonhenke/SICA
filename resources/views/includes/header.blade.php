<div class="navbarDiv shadow-sm">
    <style>
        .navbarDiv {
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar {
            transition: all 0.3s ease-in-out;
            background-color: var(--color-secondary) !important;
        }

        /* Alinhamento perfeito do logo */
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem; /* espaço entre logo e texto */
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
            top: 1px; /* ajuste fino */
            color: var(--color-menu-text);
        }

        .themeIcon{
            color: var(--color-menu-text) !important;
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
            background-color: var(--color-accent);
            /* color: var(--color-text) !important; */
        }

        .dropdown-menu {
            border-radius: 0.5rem;
            overflow: hidden;
            background-color: var(--color-surface);
            color: var(--color-text);
        }

        .dropdown-item:hover {
            background-color: var(--color-herb-light);
        }

        .btn-outline-light {
            transition: all 0.2s ease-in-out;
            border-color: var(--color-surface);
            color: var(--color-surface);
        }

        .btn-outline-light:hover {
            background-color: var(--color-surface) !important;
            color: var(--color-primary) !important;
        }

        /* Botão do usuário (logado) */
        .nav-link.dropdown-toggle.bg-white {
            background-color: var(--color-surface) !important;
            color: var(--color-primary) !important;
        }

        .nav-link.dropdown-toggle.bg-white:hover {
            background-color: var(--color-herb-light) !important;
        }
    </style>

    <nav class="navbar navbar-expand-lg px-3">
        {{-- Logo (navbar-brand) --}}
        <a class="navbar-brand" href="/">
            <img src="{{ asset('images/logo_sica.png') }}" alt="SICA Logo" class="me-2">
            <span class="fw-bold">S.I.C.A</span>
        </a>

        {{-- Botão do menu mobile --}}
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Menu --}}
        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <ul class="navbar-nav mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="/">Página Inicial</a></li>
                <li class="nav-item"><a class="nav-link" href="/plants_list">Lista de Plantas</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('topics.index') }}">Tópicos</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('products.index') }}">Produtos</a></li>

                @if(Auth::check() && Auth::user()->user_lvl === 'admin')
                    <li class="nav-item"><a class="nav-link" href="/add_plant">Adicionar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/users_list">Usuários</a></li>
                @endif

                {{-- <li class="nav-item"><a class="nav-link" href="#aboutMe">Sobre Nós</a></li> --}}
                {{-- <li class="nav-item"><a class="nav-link" href="#contactMe">Contato</a></li> --}}
                {{-- Troca de temas --}}
                {{-- <li class="nav-item ms-2">
                    <div class="form-check form-switch d-flex align-items-center">
                        <input class="form-check-input" type="checkbox" id="theme-toggle" style="cursor:pointer;">
                        <label class="form-check-label ms-2" for="theme-toggle">
                            <i class="bi bi-sun-fill themeIcon" id="themeIcon"></i>
                        </label>
                    </div>
                </li> --}}

                <li class="nav-item">
                    <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                        <i class="bi bi-cart3"></i>
                        @if(session('cart') && count(session('cart')) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ count(session('cart')) }}
                            </span>
                        @endif
                    </a>
                </li>

                {{-- Se o usuário estiver logado --}}
                @if(Auth::check())
                    <li class="nav-item dropdown ms-lg-3">
                        <a class="nav-link dropdown-toggle fw-bold px-3 rounded-pill" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                            {{ strtok(Auth::user()->name, ' ') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/edit_profile">Editar Perfil</a></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                   Sair
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-light px-3 py-1 rounded-pill" href="{{ route('login') }}">Entrar</a>
                    </li>
                @endif
            </ul>
        </div>

        {{-- Formulário de logout oculto --}}
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </nav>
</div>
