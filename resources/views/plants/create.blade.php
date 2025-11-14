@extends('layouts.main')

@section('content')
<style>
    .create-plant-container {
        max-width: 850px;
        margin: 3rem auto;
        background-color: var(--color-surface-secondary);
        padding: 2.5rem;
        border-radius: 1rem;
        border: 1px solid var(--color-border);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .create-plant-title {
        text-align: center;
        color: var(--color-menu-bg);
        font-weight: 800;
        font-size: 2rem;
        margin-bottom: 2rem;
    }

    label {
        font-weight: 600;
        color: var(--color-text);
        margin-bottom: 0.4rem;
        display: block;
    }

    .form-control {
        border: 1px solid var(--color-border);
        border-radius: 0.6rem;
        background-color: var(--color-input-bg);
        color: var(--color-input-text);
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--color-menu-bg);
        box-shadow: 0 0 0 0.2rem rgba(76, 99, 63, 0.25);
        background-color: var(--color-surface-primary);
        color: var(--color-text);
    }

    textarea.form-control {
        resize: vertical;
    }

    /* Upload de arquivo */
    .file-label {
        display: block;
        background-color: var(--color-menu-bg);
        color: #fff;
        padding: 0.6rem 1rem;
        border-radius: 0.5rem;
        text-align: center;
        cursor: pointer;
        transition: background 0.3s ease;
        font-weight: 600;
    }

    .file-label:hover {
        background-color: var(--color-accent);
    }

    input[type="file"] {
        display: none;
    }

    /* Botão principal */
    .btn-submit {
        background-color: var(--color-menu-bg);
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
        background-color: var(--color-accent);
        transform: translateY(-2px);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    /* Pré-visualização de imagens */
    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
        margin-top: 1rem;
        justify-content: center;
    }

    .preview-item {
        position: relative;
        width: 120px;
        height: 120px;
        border-radius: 0.5rem;
        overflow: hidden;
        cursor: grab;
        border: 2px solid var(--color-border);
        background-color: var(--color-surface-secondary);
        transition: border-color 0.2s, transform 0.2s;
    }

    .preview-item.dragging {
        opacity: 0.6;
        transform: scale(0.95);
        border-color: var(--color-menu-bg);
    }

    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 0.5rem;
    }

    /* Botão remover imagem */
    .remove-btn {
        position: absolute;
        top: 3px;
        right: 3px;
        background-color: #c0392b; /* Vermelho elegante */
        border: none;
        color: #fff;
        font-size: 0.9rem;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.25);
    }

    /* Erros */
    .is-invalid {
        border-color: #c0392b !important;
        box-shadow: 0 0 0 0.2rem rgba(192, 57, 43, 0.25);
    }

    .invalid-feedback {
        color: #c0392b;
        font-size: 0.9rem;
        margin-top: 0.3rem;
        font-weight: 500;
    }
</style>

<div class="create-plant-container">
    <h2 class="create-plant-title">🌱 Adicionar Nova Planta</h2>

    <form method="POST" action="{{ route('plants.store') }}" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- Nome Científico --}}
        <div class="form-group mb-3">
            <label for="scientific_name">Nome Científico</label>
            <input required type="text" class="form-control @error('scientific_name') is-invalid @enderror" id="scientific_name" name="scientific_name"
                value="{{ old('scientific_name') }}">
            @error('scientific_name')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        {{-- Nome Popular --}}
        <div class="form-group mb-3">
            <label for="popular_name">Nome Popular</label>
            <input type="text" class="form-control @error('popular_name') is-invalid @enderror" id="popular_name" name="popular_name"
                value="{{ old('popular_name') }}" required>
            @error('popular_name')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        {{-- Habitat --}}
        <div class="form-group mb-3">
            <label for="habitat">Habitat</label>
            <textarea class="form-control @error('habitat') is-invalid @enderror" id="habitat" name="habitat" rows="3" required>{{ old('habitat') }}</textarea>
            @error('habitat')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        {{-- Partes Utilizadas --}}
        <div class="form-group mb-3">
            <label>Partes Utilizadas</label><br>
            @foreach(['Folhas', 'Raízes', 'Sementes', 'Flores', 'Ramos'] as $parte)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="useful_parts[]" value="{{ $parte }}"
                        id="parte_{{ $parte }}"
                        {{ in_array($parte, old('useful_parts', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="parte_{{ $parte }}">{{ $parte }}</label>
                </div>
            @endforeach
            @error('useful_parts')
                <small class="invalid-feedback d-block">{{ $message }}</small>
            @enderror
        </div>

        {{-- Características --}}
        <div class="form-group mb-3">
            <label for="characteristics">Características</label>
            <textarea class="form-control @error('characteristics') is-invalid @enderror" id="characteristics" name="characteristics" rows="3" required>{{ old('characteristics') }}</textarea>
            @error('characteristics')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        {{-- Observações --}}
        <div class="form-group mb-3">
            <label for="observations">Observações</label>
            <textarea class="form-control @error('observations') is-invalid @enderror" id="observations" name="observations" rows="3" required>{{ old('observations') }}</textarea>
            @error('observations')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        {{-- Uso Popular --}}
        <div class="form-group mb-3">
            <label for="popular_use">Uso Popular</label>
            <textarea class="form-control @error('popular_use') is-invalid @enderror" id="popular_use" name="popular_use" rows="6" required>{{ old('popular_use') }}</textarea>
            @error('popular_use')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        {{-- Composição Química --}}
        <div class="form-group mb-3">
            <label for="chemical_composition">Composição Química</label>
            <input type="text" class="form-control @error('chemical_composition') is-invalid @enderror" id="chemical_composition" name="chemical_composition"
                value="{{ old('chemical_composition') }}">
            @error('chemical_composition')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        {{-- Contraindicações --}}
        <div class="form-group mb-3">
            <label for="contraindications">Contraindicações</label>
            <input type="text" class="form-control @error('contraindications') is-invalid @enderror" id="contraindications" name="contraindications"
                value="{{ old('contraindications') }}">
            @error('contraindications')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        {{-- Modos de Uso --}}
        <div class="form-group mb-3">
            <label for="mode_of_use">Modos de Uso</label>
            <input type="text" class="form-control @error('mode_of_use') is-invalid @enderror" id="mode_of_use" name="mode_of_use"
                value="{{ old('mode_of_use') }}">
            @error('mode_of_use')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        {{-- QR Code manual --}}
        <div class="form-group mb-3">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="manual_qr_toggle">
                <label class="form-check-label fw-semibold" for="manual_qr_toggle">
                    Definir QR Code manualmente
                </label>
            </div>

            <div id="qr_code_wrapper" class="d-none">
                <label for="qr_code">QR Code (opcional)</label>
                <input type="text" class="form-control" id="qr_code" name="qr_code"
                    placeholder="Link ou identificador do QR Code manual"
                    value="{{ old('qr_code') }}">
                <small class="text-muted">Se não preencher, o QR Code será gerado automaticamente.</small>
            </div>
        </div>

        {{-- Imagens --}}
        <div class="form-group mb-3">
            <label for="images" class="file-label">📸 Escolher Imagens (máximo 5)</label>
            <input type="file" id="images" name="images[]" accept="image/*" multiple required>
            @error('images.*')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
            <small id="imageError" class="invalid-feedback d-none">Você pode selecionar no máximo 5 imagens.</small>
            <div id="imagePreview" class="image-preview-container"></div>
        </div>

        {{-- Referências --}}
        <div class="form-group mb-3">
            <label for="info_references">Referências</label>
            <input type="text" class="form-control @error('info_references') is-invalid @enderror" id="info_references" name="info_references"
                value="{{ old('info_references') }}" required>
            @error('info_references')
                <small class="invalid-feedback">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">💾 Salvar Planta</button>
        <a href="{{ route('plants.index') }}" class="btn-cancel">❌ Cancelar</a>
    </form>
</div>

{{-- Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // QR Code manual toggle
    const toggle = document.getElementById('manual_qr_toggle');
    const qrWrapper = document.getElementById('qr_code_wrapper');
    const qrInput = document.getElementById('qr_code');

    qrWrapper.classList.add('d-none');
    qrInput.disabled = true;

    toggle.addEventListener('change', function () {
        if (this.checked) {
            qrWrapper.classList.remove('d-none');
            qrInput.disabled = false;
        } else {
            qrWrapper.classList.add('d-none');
            qrInput.disabled = true;
            qrInput.value = '';
        }
    });

    // Imagens com preview e limite
    let selectedImages = [];
    const input = document.getElementById('images');
    const preview = document.getElementById('imagePreview');
    const errorMsg = document.getElementById('imageError');
    const form = document.querySelector('form');
    let draggedIndex = null;

    function renderPreviews() {
        preview.innerHTML = '';
        selectedImages.forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'preview-item';
            div.draggable = true;
            div.dataset.index = index;

            const img = document.createElement('img');
            const reader = new FileReader();
            reader.onload = e => img.src = e.target.result;
            reader.readAsDataURL(file);

            const remove = document.createElement('button');
            remove.className = 'remove-btn';
            remove.innerHTML = '×';
            remove.addEventListener('click', () => {
                selectedImages.splice(index, 1);
                renderPreviews();
            });

            div.addEventListener('dragstart', e => {
                draggedIndex = index;
                div.classList.add('dragging');
            });
            div.addEventListener('dragend', () => div.classList.remove('dragging'));
            div.addEventListener('dragover', e => e.preventDefault());
            div.addEventListener('drop', e => {
                e.preventDefault();
                const targetIndex = Number(div.dataset.index);
                const dragged = selectedImages[draggedIndex];
                selectedImages.splice(draggedIndex, 1);
                selectedImages.splice(targetIndex, 0, dragged);
                renderPreviews();
            });

            div.appendChild(img);
            div.appendChild(remove);
            preview.appendChild(div);
        });
    }

    input.addEventListener('change', e => {
        const files = Array.from(e.target.files);
        if (selectedImages.length + files.length > 5) {
            errorMsg.classList.remove('d-none');
            return;
        } else {
            errorMsg.classList.add('d-none');
        }
        selectedImages = selectedImages.concat(files);
        renderPreviews();
    });

    form.addEventListener('submit', e => {
        if (selectedImages.length === 0) return;
        const dt = new DataTransfer();
        selectedImages.forEach(f => dt.items.add(f));
        input.files = dt.files;
    });
});
</script>
@endsection
