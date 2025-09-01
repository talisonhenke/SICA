<div class="navbarDiv d-sm-block d-md-block d-lg-flex d-xl-flex border-bottom border-white">
    <nav class="navbarMy navbar navbar-expand-lg col-12">
        <!-- Logo -->
        <p class="ms-3 text-white navbar-brand">
            <a href="/" class="text-decoration-none text-reset">S.I.C.A</a>
        </p>

        <!-- Ícones para telas pequenas -->
        <div class="d-lg-none d-flex align-items-center ms-auto">
            <!-- Ícone menu do usuário -->
            @if(Auth::check())
            <div class="dropdown">
                <button class="btn btn-outline-light p-0 border-0 bg-transparent" type="button" id="userMenuMobile" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle menu-user-button"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuMobile">
                    <li><a class="dropdown-item" href="/edit_profile">Editar Perfil</a></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                           Sair
                        </a>
                    </li>
                </ul>
            </div>
            @endif

            <!-- Ícone menu do sistema -->
            <button class="navbar-toggler me-2" type="button" data-bs-toggle="collapse" data-bs-target="#systemMenu" aria-controls="systemMenu" aria-expanded="false" aria-label="Toggle navigation">>
                <i class="bi bi-list menu-button"></i>
            </button>
        </div>

        <!-- Menu do sistema expandido -->
        <div class="collapse navbar-collapse" id="systemMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 col-12 justify-content-end text-center">
                <li class="nav-item"><a class="nav-link" href="/">Página Inicial</a></li>
                <li class="nav-item"><a class="nav-link" href="/plants_list">Lista de plantas</a></li>
                @if(Auth::check() && Auth::user()->user_lvl === 'admin')
                    <li class="nav-item"><a class="nav-link" href="/add_plant">Adicionar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/users_list">Lista de Usuários</a></li>
                @endif
                <li class="nav-item"><a class="nav-link" href="#aboutMe">Sobre nós</a></li>
                <li class="nav-item"><a class="nav-link" href="#contactMe">Contato</a></li>

                <!-- Menu do usuário em telas grandes -->
                @if(Auth::check())
                    <li class="nav-item dropdown d-none d-lg-block">
                        <a class="nav-link dropdown-toggle mx-4 bg-white text-dark fw-bold" href="#" id="userMenuDesktop" role="button" data-bs-toggle="dropdown">
                            Bem-vindo, {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuDesktop">
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
                    <li class="nav-item d-block">
                        <a class="nav-link" href="{{ route('login') }}">Entrar</a>
                    </li>
                @endif
            </ul>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </nav>
</div>
