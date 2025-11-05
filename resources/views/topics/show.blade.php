@extends('layouts.main')

@section('content')
<style>
    .topic-container {
        max-width: 850px;
        margin: 3rem auto;
        background-color: var(--color-surface);
        padding: 2.5rem;
        border-radius: 1rem;
        box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .topic-title {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--color-secondary);
        text-align: center;
        margin-bottom: 0.8rem;
    }

    .topic-description {
        font-size: 1.1rem;
        color: var(--color-text-secondary);
        text-align: center;
        margin-bottom: 1.8rem;
    }

    .topic-image {
        display: block;
        width: 100%;
        max-width: 600px;
        height: auto;
        border-radius: 0.75rem;
        margin: 0 auto 1.5rem;
        box-shadow: 0 4px 14px rgba(0,0,0,0.15);
    }

    .topic-content {
        text-align: justify;
        line-height: 1.8;
        color: var(--color-text);
        font-size: 1.05rem;
        white-space: pre-line;
    }

    .button-group {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.8rem;
        margin-top: 2rem;
    }

    .btn-action {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        border-radius: 0.6rem;
        font-weight: 600;
        text-decoration: none;
        color: #fff;
        transition: background 0.3s ease, transform 0.1s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
    }

    .btn-edit {
        background-color: var(--color-accent);
    }

    .btn-edit:hover {
        background-color: var(--color-secondary);
    }

    .btn-delete {
        background-color: var(--color-danger);
    }

    .btn-delete:hover {
        background-color: #b71c1c;
    }

    .btn-back {
        background-color: var(--color-primary);
    }

    .btn-back:hover {
        background-color: var(--color-primary-light);
    }

    form {
        display: inline;
    }
</style>

<div class="topic-container">
    <div class="button-group mb-4">
        {{-- Voltar --}}
        <a href="{{ route('topics.index') }}" class="btn-action btn-back">← Voltar</a>

        {{-- Botões de admin --}}
        @if(Auth::check() && Auth::user()->user_lvl === 'admin')
            <a href="{{ route('topics.edit', $topic->id) }}" class="btn-action btn-edit">✏️ Editar</a>

            <form action="{{ route('topics.destroy', $topic->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este tópico?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-action btn-delete">🗑️ Excluir</button>
            </form>
        @endif
    </div>
    <h1 class="topic-title">{{ $topic->title }}</h1>
    <p class="topic-description">{{ $topic->description }}</p>

    @if($topic->image)
        <img src="{{ asset($topic->image) }}" alt="{{ $topic->title }}" class="topic-image">
    @endif

    <div class="topic-content">
        {!! nl2br(e($topic->content)) !!}
    </div>
</div>
@endsection
