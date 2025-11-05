@extends('layouts.main')

@section('content')
<style>
    .topic-card {
        position: relative;
        border: 1px solid #e0e0e0;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        transition: all 0.2s ease-in-out;
        background-color: var(--color-surface);
        height: 100%;
    }

    .topic-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 5px 10px rgba(0,0,0,0.15);
    }

    .topic-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background-color: #fafafa;
        border-bottom: 1px solid #ddd;
    }

    /* 🔘 Switch de destaque */
    .featured-switch {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 44px;
        height: 24px;
        background-color: #ccc;
        border-radius: 12px;
        cursor: pointer;
        transition: background-color 0.3s;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
    }

    .featured-switch::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        background-color: white;
        border-radius: 50%;
        transition: transform 0.3s;
    }

    .featured-switch.active {
        background-color: var(--color-success);
    }

    .featured-switch.active::after {
        transform: translateX(20px);
    }

    .featured-label {
        position: absolute;
        top: 38px;
        right: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #333;
        background: rgba(255,255,255,0.9);
        padding: 2px 6px;
        border-radius: 8px;
    }

    .topic-body {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        height: 100%;
    }

    .topic-title {
        font-weight: 700;
        font-size: 1.3rem;
        color: var(--color-primary);
        margin-bottom: 0.5rem;
        text-align: center;
    }

    .topic-description {
        color: var(--color-text-secondary);
        font-size: 0.95rem;
        text-align: center;
        margin-bottom: 1rem;
        height: 3.6em;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .topic-main-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .topic-admin-actions {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
    }

    .topic-admin-actions .btn {
        padding: 0.25rem 0.6rem;
        font-size: 0.9rem;
    }
</style>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold primaryTitles">Tópicos</h2>
        <a href="{{ route('topics.create') }}" class="btn btn-success">+ Adicionar Tópico</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        @foreach ($topics as $topic)
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="topic-card">
                    {{-- Switch de destaque (só admins veem) --}}
                    @if(Auth::check() && Auth::user()->user_lvl === 'admin')
                        <div class="featured-switch {{ $topic->featured ? 'active' : '' }}" 
                            data-id="{{ $topic->id }}"
                            onclick="toggleFeatured({{ $topic->id }})"></div>
                        <div class="featured-label" data-id="{{ $topic->id }}">
                            {{ $topic->featured ? 'Em destaque' : 'Normal' }}
                        </div>
                    @endif

                    {{-- Imagem --}}
                    @if($topic->image)
                        <img src="{{ asset('images/topics/' . $topic->id . '/' . basename($topic->image)) }}" 
                             alt="{{ $topic->title }}" 
                             class="topic-image">
                    @else
                        <img src="{{ asset('images/default.jpg') }}" 
                             alt="{{ $topic->title }}" 
                             class="topic-image">
                    @endif

                    {{-- Corpo --}}
                    <div class="topic-body">
                        <h5 class="topic-title">{{ $topic->title }}</h5>
                        <p class="topic-description">{{ $topic->description }}</p>

                        {{-- Botão de leitura --}}
                        <div class="topic-main-actions">
                            <a href="{{ route('topics.show', $topic->id) }}" class="btn btn-primary btn-sm">Ler mais</a>
                        </div>

                        {{-- Ações administrativas --}}
                        @if(Auth::check() && Auth::user()->user_lvl === 'admin')
                            <div class="topic-admin-actions">
                                <a href="{{ route('topics.edit', $topic->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </a>
                                <form action="{{ route('topics.destroy', $topic->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Tem certeza que deseja excluir este tópico?')">
                                        <i class="bi bi-trash"></i> Excluir
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
function toggleFeatured(id) {
    const switchElement = document.querySelector(`.featured-switch[data-id="${id}"]`);
    const isActive = switchElement.classList.toggle('active'); // alterna visualmente

    fetch(`/topics/${id}/toggle-featured`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ featured: isActive })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert(data.message);
            switchElement.classList.toggle('active'); // desfaz se não deu certo
        } else {
            const label = document.querySelector(`.featured-label[data-id="${id}"]`);
            label.textContent = isActive ? 'Em destaque' : 'Normal';
        }
    })
    .catch(error => console.error('Erro ao alterar destaque:', error));
}
</script>

@endsection
