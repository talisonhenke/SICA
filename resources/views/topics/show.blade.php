@extends('layouts.main')

@section('content')
<style>
    .topic-container {
        max-width: 850px;
        margin: 2rem auto;
        background-color: var(--color-surface);
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .topic-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 0.5rem;
        text-align: center;
    }

    .topic-description {
        font-size: 1.1rem;
        color: var(--color-text-secondary);
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .topic-image {
        display: block;
        width: 100%;
        max-width: 600px;
        height: 320px;
        object-fit: cover;
        border-radius: 0.75rem;
        margin: 0 auto 1.5rem auto;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }

    .topic-content {
        text-align: justify;
        line-height: 1.8;
        color: var(--color-text);
        font-size: 1.05rem;
        white-space: pre-line;
    }

    .back-button {
        display: inline-block;
        text-decoration: none;
        background-color: var(--color-primary);
        color: var(--color-surface);
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
        margin-bottom: 1.5rem;
    }

    .back-button:hover {
        background-color: var(--color-primary-light);
        color: var(--color-text);
    }
</style>

<div class="topic-container">
    {{-- Botão de Voltar --}}
    <a href="{{ url()->previous() === url()->current() ? route('topics.index') : url()->previous() }}" 
       class="back-button">
        ← Voltar
    </a>

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
