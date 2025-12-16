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
        color: var(--color-text);
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
        margin-bottom: 1rem;
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
        transition: background 0.2s ease, transform 0.1s ease;
    }

    .verify-btn:hover {
        background: var(--color-primary-dark);
        transform: translateY(-1px);
    }

    .verify-hint {
        font-size: 0.85rem;
        color: var(--color-muted);
        margin-top: 0.8rem;
    }
</style>

<div class="verify-wrapper">
    <div class="verify-card">

        <div class="verify-icon">🔐</div>

        <h1 class="verify-title">Redefinir senha</h1>

        <p class="verify-text">
            Crie uma nova senha segura para acessar sua conta.
        </p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <input type="password"
                   name="password"
                   class="verify-input @error('password') is-invalid @enderror"
                   placeholder="Nova senha"
                   required>

            @error('password')
                <div class="verify-hint">{{ $message }}</div>
            @enderror

            <input type="password"
                   name="password_confirmation"
                   class="verify-input"
                   placeholder="Confirmar nova senha"
                   required>

            <button type="submit" class="verify-btn">
                Redefinir senha
            </button>
        </form>

        <p class="verify-hint">
            Escolha uma senha forte e fácil de lembrar.
        </p>

    </div>
</div>
@endsection
