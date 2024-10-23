{{-- Menu bar --}}
<div class="navbarDiv d-sm-block d-md-block d-lg-flex d-xl-flex border-bottom border-white">
    <nav class="navbarMy navbar navbar-expand-lg col-sm-12 col-md-12 col-lg-12 col-xl-12">
        <p class="ms-3 text-white navbar-brand"> <!-- mt-3 removed -->
            <a href="/" class="text-decoration-none text-reset">S.I.C.A</a>
        </p>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list menu-button"></i>
        </button>
        <div class="collapse navbar-collapse text-xs-center text-sm-center text-lg-end mx-sm-0 mx-lg-3" id="navbarTogglerDemo01">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 col-12 justify-content-end">
                <li class="nav-item text-center">
                    <a class="nav-link" href="/">Página Inicial</a>
                </li>
                <li class="nav-item text-center">
                    <a class="nav-link" href="/plants_list">Lista de plantas</a>
                </li>
                <li class="nav-item text-center">
                    <a class="nav-link" href="/add_plant">Adicionar</a>
                </li>
                <li class="nav-item text-center">
                    <a class="nav-link" href="#aboutMe">Sobre nós</a>
                </li>
                <li class="nav-item text-center">
                    <a class="nav-link" href="#contactMe">Contato</a>
                </li>
                
                @if(Auth::check())
                    <li class="nav-item text-center">
                        <a class="nav-link mx-4" href="#">Bem-vindo, {{ Auth::user()->name }}</a>
                    </li>
                    <li class="nav-item text-center">
                        <a class="nav-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                           Sair
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                @else
                    <li class="nav-item text-center">
                        <a class="nav-link" href="{{ route('login') }}">Entrar</a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
  </div>
  