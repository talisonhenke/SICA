@extends('layouts.auth')

@section('content')
<style>
    .verify-wrapper {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .verify-card {
        background: var(--color-surface-primary, #fff);
        border-radius: 16px;
        padding: 2.5rem 2.8rem;
        max-width: 460px;
        width: 100%;
        text-align: center;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }

    .verify-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .verify-title {
        font-size: 1.6rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .verify-text {
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        color: var(--color-text);
    }

    .verify-input {
        width: 100%;
        padding: 0.75rem;
        border-radius: 10px;
        border: 1px solid #ddd;
        margin-bottom: 1.2rem;
        font-size: 0.95rem;
    }

    .verify-btn {
        width: 100%;
        padding: 0.75rem;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: bold;
        background: var(--color-primary);
        color: #fff;
    }

    .verify-hint {
        font-size: 0.85rem;
        color: var(--color-muted);
    }
</style>

<div class="verify-wrapper">
    <div class="verify-card">

        @if (session('status'))

            {{-- ESTADO: E-MAIL ENVIADO --}}
            <div class="verify-icon">📩</div>

            <h1 class="verify-title">Confira seu e-mail</h1>

            <p class="verify-text">
                Se o e-mail informado estiver correto e verificado,
                você receberá um link para redefinir sua senha.
            </p>

            <p class="verify-hint">
                O link expira em alguns minutos.
                Caso não encontre, verifique o spam.
            </p>

        @else

            {{-- ESTADO: FORMULÁRIO --}}
            <div class="verify-icon">🔑</div>

            <h1 class="verify-title">Esqueceu sua senha?</h1>

            <p class="verify-text">
                Informe o e-mail da sua conta para receber um link
                de redefinição de senha.
            </p>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <input type="email"
                       name="email"
                       class="verify-input @error('email') is-invalid @enderror"
                       placeholder="Seu e-mail"
                       required>

                @error('email')
                    <div class="verify-hint">{{ $message }}</div>
                @enderror

                <button type="submit" class="verify-btn">
                    Enviar link de redefinição
                </button>
            </form>

        @endif

    </div>
</div>
@endsection
