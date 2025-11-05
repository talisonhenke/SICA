@extends('layouts.main')

@section('content')
<style>
    .create-topic-container {
        max-width: 800px;
        margin: 3rem auto;
        background-color: var(--color-surface);
        padding: 2.5rem;
        border-radius: 1rem;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    .create-topic-title {
        text-align: center;
        color: var(--color-secondary);
        font-weight: 800;
        font-size: 2rem;
        margin-bottom: 2rem;
    }

    label {
        font-weight: 600;
        color: var(--color-primary-dark);
        margin-bottom: 0.4rem;
        display: block;
    }

    .form-control {
        border: 1px solid var(--color-muted);
        border-radius: 0.6rem;
        background-color: var(--color-bg);
        color: var(--color-text);
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--color-accent);
        box-shadow: 0 0 0 0.2rem rgba(108, 139, 88, 0.25);
        background-color: #fff;
        color: var(--color-text-dark);
    }

    /* Campos com erro */
    .is-invalid {
        border-color: var(--color-danger) !important;
        box-shadow: 0 0 0 0.2rem rgba(217, 83, 79, 0.25);
    }

    .invalid-feedback {
        color: var(--color-danger);
        font-size: 0.9rem;
        margin-top: 0.3rem;
        font-weight: 500;
    }

    textarea.form-control {
        resize: vertical;
    }

    .btn-submit {
        background-color: var(--color-accent);
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 0.6rem;
        padding: 0.75rem 1.5rem;
        transition: background 0.3s ease, transform 0.1s ease;
        width: 100%;
        margin-top: 1rem;
    }

    .btn-submit:hover {
        background-color: var(--color-secondary);
        transform: translateY(-2px);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .file-label {
        display: block;
        background-color: var(--color-primary-light);
        color: #fff;
        padding: 0.6rem 1rem;
        border-radius: 0.5rem;
        text-align: center;
        cursor: pointer;
        transition: background 0.3s ease;
        font-weight: 600;
    }

    .file-label:hover {
        background-color: var(--color-primary);
    }

    input[type="file"] {
        display: none;
    }

    .preview-image {
        display: block;
        margin: 1rem auto;
        max-width: 100%;
        border-radius: 0.5rem;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    }

    .text-muted {
        font-size: 0.85rem;
        color: var(--color-muted);
        text-align: center;
        display: block;
        margin-top: 0.5rem;
    }
</style>

<div class="create-topic-container">
    <h2 class="create-topic-title">📝 Editar Tópico</h2>

    <form action="{{ route('topics.update', $topic->id) }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')

        <div class="form-group mb-4">
            <label for="title">Título</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror"
                   name="title" value="{{ old('title', $topic->title) }}" placeholder="Digite o título do tópico..." required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="description">Descrição</label>
            <textarea class="form-control @error('description') is-invalid @enderror"
                      name="description" rows="3" placeholder="Uma breve descrição sobre o conteúdo..." required>{{ old('description', $topic->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="content">Conteúdo</label>
            <textarea class="form-control @error('content') is-invalid @enderror"
                      name="content" rows="5" placeholder="Escreva aqui o conteúdo completo do tópico..." required>{{ old('content', $topic->content) }}</textarea>
            @error('content')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4 text-center">
            <label for="image" class="file-label">📷 Alterar Imagem do Tópico</label>
            <input type="file" id="image" name="image" accept="image/*">

            @if($topic->image)
                <img id="preview" src="{{ asset($topic->image) }}" class="preview-image" alt="Imagem atual do tópico">
                <small class="text-muted">A imagem atual será substituída se uma nova for escolhida.</small>
            @else
                <img id="preview" class="preview-image d-none" alt="Prévia da imagem">
            @endif

            @error('image')
                <div class="invalid-feedback d-block text-center">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">💾 Atualizar Tópico</button>
        <a href="{{ route('topics.index') }}" class="btn-cancel">❌ Cancelar</a>
    </form>
</div>

<script>
    // Preview da nova imagem
    document.getElementById('image').addEventListener('change', function (event) {
        const [file] = event.target.files;
        const preview = document.getElementById('preview');
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        }
    });
</script>
@endsection
