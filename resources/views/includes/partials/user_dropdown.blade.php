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
        <a class="btn btn-outline-light px-3 py-1 rounded-pill mt-lg-0 mt-sm-2" href="{{ route('login') }}">Entrar</a>
    </li>
@endif
