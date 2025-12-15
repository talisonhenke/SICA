@extends('layouts.auth')

@section('content')
    <style>
        /* ===== VERIFY EMAIL ===== */

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
            color: var(--color-text);
            margin-bottom: 1rem;
        }

        .verify-text {
            font-size: 0.95rem;
            color: var(--color-text);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .verify-alert {
            background: var(--color-bg, #e8f8f0);
            color: var(--color-success, #1f7a4f);
            padding: 0.8rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 1.2rem;
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
            margin-top: 1.2rem;
            font-size: 0.85rem;
            color: var(--color-muted);
        }
    </style>

    <div class="verify-wrapper">
        <div class="verify-card">

            <div class="verify-icon">
                📧
            </div>

            <h1 class="verify-title">Verifique seu e-mail</h1>

            <p class="verify-text">
                Enviamos um link de verificação para o seu e-mail.
                Antes de continuar, confirme seu endereço clicando no link recebido.
            </p>

            @if (session('status'))
                <div class="verify-alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="verify-btn">
                    Reenviar e-mail de verificação
                </button>
            </form>

            <p class="verify-hint">
                Não encontrou o e-mail? Verifique a caixa de spam.
            </p>

        </div>
    </div>
@endsection
