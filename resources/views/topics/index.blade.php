@extends('layouts.main')

@section('content')
<style>
    .topics-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .topics-title {
        text-align: center;
        font-weight: 700;
        color: var(--color-primary);
        margin-bottom: 2rem;
    }

    .topics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .topic-card {
        background: var(--color-accent);
        border-radius: 1rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .topic-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }

    .topic-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .topic-body {
        padding: 1rem 1.2rem;
        flex: 1;
        background: var(--color-surface);
        border-top: solid 1px var(--color-text-dark);
    }

    .topic-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--color--dark);
        margin-bottom: 0.5rem;
    }

    .topic-description {
        color: var(--color-text-dark);
        font-size: 0.95rem;
        margin-bottom: 1rem;
        height: 3.6em;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .topic-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        background: var(--color-primary-light);
        border-top: 1px solid rgba(0,0,0,0.05);
    }

    .topic-actions .btn {
        font-size: 0.85rem;
        padding: 0.4rem 0.7rem;
        border-radius: 0.5rem;
    }

    /* 🔘 Switch de destaque */
    .featured-switch {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 500;
        color: var(--color-text);
    }

    .featured-switch input[type="checkbox"] {
        width: 42px;
        height: 22px;
        appearance: none;
        background-color: #ccc;
        border-radius: 11px;
        position: relative;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .featured-switch input[type="checkbox"]::before {
        content: "";
        position: absolute;
        width: 18px;
        height: 18px;
        background: white;
        border-radius: 50%;
        top: 2px;
        left: 2px;
        transition: transform 0.2s;
    }

    .featured-switch input[type="checkbox"]:checked {
        background-color: var(--color-primary);
    }

    .featured-switch input[type="checkbox"]:checked::before {
        transform: translateX(20px);
    }

    /* Botões com ícones */
    .btn i {
        margin-right: 4px;
        font-size: 0.9em;
    }
</style>

<div class="topics-container">
    <h2 class="topics-title">Tópicos Cadastrados</h2>

    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    <div class="topics-grid">
        @foreach($topics as $topic)
            <div class="topic-card">
                @if($topic->image)
                    <img src="{{ asset('images/topics/' . $topic->id . '/' . basename($topic->image)) }}" 
                         alt="{{ $topic->title }}" class="topic-image">
                @endif

                <div class="topic-body">
                    <h5 class="topic-title">{{ $topic->title }}</h5>
                    <p class="topic-description">{{ $topic->description }}</p>
                    <a href="{{ route('topics.show', $topic->id) }}" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-book"></i> Ler mais
                    </a>
                </div>

                @if(Auth::check() && Auth::user()->user_lvl === 'admin')
                    <div class="topic-actions">
                        <div class="featured-switch">
                            <input type="checkbox" 
                                   id="featured_{{ $topic->id }}" 
                                   class="featured-toggle"
                                   data-id="{{ $topic->id }}"
                                   {{ $topic->featured ? 'checked' : '' }}>
                            <label for="featured_{{ $topic->id }}">Destaque</label>
                        </div>
                        <div>
                            <a href="{{ route('topics.edit', $topic->id) }}" class="btn btn-warning btn-sm me-1">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>
                            <form action="{{ route('topics.destroy', $topic->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('.featured-toggle');

    toggles.forEach(toggle => {
        toggle.addEventListener('change', async (e) => {
            const topicId = e.target.dataset.id;
            const isChecked = e.target.checked;

            const response = await fetch(`/topics/${topicId}/toggle-featured`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ featured: isChecked })
            });

            const result = await response.json();

            if (!result.success) {
                alert(result.message);
                e.target.checked = !isChecked; // reverte o estado
            }
        });
    });
});
</script>
@endsection
