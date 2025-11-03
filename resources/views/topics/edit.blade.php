@extends('layouts.main')

@section('content')
<style>
    .edit-container {
        max-width: 700px;
        margin: 2rem auto;
        background-color: var(--color-surface);
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .edit-container h2 {
        color: var(--color-primary);
        font-weight: 700;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 500;
        color: var(--color-text);
    }

    .image-preview {
        display: block;
        margin: 1rem auto;
        max-width: 50%;
        height: 50%;
        object-fit: cover;
        border-radius: 0.75rem;
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }

    .btn-success {
        display: block;
        width: 100%;
        padding: 0.75rem;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 0.6rem;
    }
</style>

<div class="edit-container">
    <h2>Editar Tópico</h2>

    <form action="{{ route('topics.update', $topic->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" class="form-control" name="title" value="{{ old('title', $topic->title) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="description" class="form-label">Descrição</label>
            <textarea class="form-control" name="description" rows="3" required>{{ old('description', $topic->description) }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label for="content" class="form-label">Conteúdo</label>
            <textarea class="form-control" name="content" rows="6" required>{{ old('content', $topic->content) }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label for="image" class="form-label">Imagem do Tópico</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            
            {{-- Preview da imagem atual --}}
            @if($topic->image)
                <img src="{{ asset($topic->image) }}" id="imagePreview" alt="Pré-visualização da Imagem" class="image-preview">
            @else
                <img id="imagePreview" alt="Pré-visualização da Imagem" class="image-preview" style="display:none;">
            @endif

            <small class="text-muted d-block mt-1">
                Selecione uma nova imagem apenas se desejar substituir a atual.
            </small>
        </div>

        <button type="submit" class="btn btn-success">Salvar Alterações</button>
    </form>
</div>

<script>
document.getElementById('image').addEventListener('change', function (event) {
    const preview = document.getElementById('imagePreview');
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
