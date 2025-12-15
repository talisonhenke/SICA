@extends('layouts.auth')

@section('content')

<style>
    :root {
        --color-primary-dark: #4A3A2A;
        --color-primary: #5E4A3B;
        --color-primary-light: #7E6B5D;

        --color-secondary: #4A633F;
        --color-accent: #6C8B58;

        --color-muted: #8E8A84;
    }

    .login-page {
        background: var(--color-primary-light);
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: 40px 0;
    }

    .login-card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    .left-panel {
        padding: 40px 45px;
        background: #fff;
    }

    .left-panel h2 {
        color: var(--color-primary-dark);
        font-weight: 700;
        margin-bottom: 25px;
    }

    .login-btn {
        background: var(--color-secondary);
        border: none;
        color: white;
        padding: 12px;
        font-size: 16px;
        width: 100%;
        border-radius: 6px;
        transition: .2s;
    }

    .login-btn:hover {
        background: var(--color-accent);
    }

    .google-btn {
        border: 2px solid var(--color-secondary);
        color: var(--color-secondary);
        padding: 12px;
        width: 100%;
        font-weight: bold;
        border-radius: 6px;
        transition: .2s;
        background: none;
    }

    .google-btn:hover {
        background: var(--color-secondary);
        color: white;
    }

    .right-panel {
        background: var(--color-primary);
        color: #fff;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

</style>

<section class="login-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">

                <div class="card login-card">
                    <div class="row g-0">

                        {{-- LEFT SIDE FORM --}}
                        <div class="col-lg-6 left-panel">

                            <div class="text-center mb-4">
                                <img src="/images/logos/logo1.png" width="140" alt="logo">
                                <h4 class="mt-2" style="color: var(--color-primary-dark)">S.I.C.A</h4>
                            </div>

                            <h2 class="text-center">Entrar</h2>

                            <form method="POST" action="{{ route('login.post') }}">
                                @csrf

                                <div class="form-group mb-3">
                                    <input type="email" name="email" class="form-control"
                                           placeholder="Seu email" required>
                                </div>

                                <div class="form-group mb-3">
                                    <input type="password" name="password" class="form-control"
                                           placeholder="Sua senha" required>
                                </div>

                                <div class="text-end mb-4">
                                    <a href="#" style="color: var(--color-muted); text-decoration: none;">Esqueceu sua senha?</a>
                                </div>

                                <button type="submit" class="login-btn">Entrar</button>
                            </form>

                            {{-- OU separator --}}
                            <div class="text-center mt-4 mb-3">
                                <span style="color: var(--color-muted)">OU</span>
                            </div>

                            <a href="{{ route('auth.google') }}">
                                <button class="google-btn">ENTRAR COM GOOGLE</button>
                            </a>

                            <div class="text-center mt-4">
                                <span class="text-dark">Não tem conta?</span>
                                <a href="{{ route('register') }}" class="fw-bold" style="color: var(--color-secondary)">
                                    Cadastre-se
                                </a>
                            </div>

                            {{-- TOAST DE ERRO / STATUS --}}
@if ($errors->any() || session('status'))
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="loginToast"
         class="toast align-items-center text-bg-danger border-0 show"
         role="alert"
         aria-live="assertive"
         aria-atomic="true">

        <div class="d-flex">
            <div class="toast-body">
                @if ($errors->any())
                    {{ $errors->first() }}
                @endif

                @if (session('status'))
                    {{ session('status') }}
                @endif
            </div>

            <button type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"
                    aria-label="Close">
            </button>
        </div>

    </div>
</div>
@endif


                        </div>

                        {{-- RIGHT PANEL --}}
                        <div class="col-lg-6 right-panel">
                            <h3 class="mb-3">Sistema de Informação sobre Chás Avaliados</h3>
                            <p class="small">
                                Consulte plantas medicinais, usos, contraindicações e mantenha sua pesquisa sempre organizada.
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
