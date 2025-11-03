@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 my-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">
                    Adicionar Nova Planta
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('plants.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Nome científico --}}
                        <div class="form-group mb-3">
                            <label for="scientific_name">Nome Científico</label>
                            <input type="text" class="form-control" id="scientific_name" name="scientific_name" required>
                        </div>

                        {{-- Nome popular --}}
                        <div class="form-group mb-3">
                            <label for="popular_name">Nome Popular</label>
                            <input type="text" class="form-control" id="popular_name" name="popular_name" required>
                        </div>

                        {{-- Habitat --}}
                        <div class="form-group mb-3">
                            <label for="habitat">Habitat</label>
                            <textarea class="form-control" id="habitat" name="habitat" rows="3" required></textarea>
                        </div>

                        {{-- Partes utilizadas --}}
                        <div class="form-group mb-3">
                            <label>Partes Utilizadas</label><br>
                            @foreach(['Folhas', 'Raízes', 'Sementes', 'Flores', 'Ramos'] as $parte)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="useful_parts[]" value="{{ $parte }}" id="parte_{{ $parte }}">
                                    <label class="form-check-label" for="parte_{{ $parte }}">{{ $parte }}</label>
                                </div>
                            @endforeach
                        </div>

                        {{-- Características --}}
                        <div class="form-group mb-3">
                            <label for="characteristics">Características</label>
                            <textarea class="form-control" id="characteristics" name="characteristics" rows="3"></textarea>
                        </div>

                        {{-- Observações --}}
                        <div class="form-group mb-3">
                            <label for="observations">Observações</label>
                            <textarea class="form-control" id="observations" name="observations" rows="3"></textarea>
                        </div>

                        {{-- Uso popular --}}
                        <div class="form-group mb-3">
                            <label for="popular_use">Uso Popular</label>
                            <textarea class="form-control" id="popular_use" name="popular_use" rows="6"></textarea>
                        </div>

                        {{-- Composição química --}}
                        <div class="form-group mb-3">
                            <label for="chemical_composition">Composição Química</label>
                            <input type="text" class="form-control" id="chemical_composition" name="chemical_composition">
                        </div>

                        {{-- Contraindicações --}}
                        <div class="form-group mb-3">
                            <label for="contraindications">Contraindicações</label>
                            <input type="text" class="form-control" id="contraindications" name="contraindications">
                        </div>

                        {{-- Modo de uso --}}
                        <div class="form-group mb-3">
                            <label for="mode_of_use">Modos de Uso</label>
                            <input type="text" class="form-control" id="mode_of_use" name="mode_of_use">
                        </div>

                        {{-- QR Code --}}
                        <div class="form-group mb-3">
                            <label for="qr_code">QR Code (opcional)</label>
                            <input type="text" class="form-control" id="qr_code" name="qr_code" placeholder="Link ou identificador do QR Code">
                        </div>

                        {{-- Imagens (até 5 arquivos) --}}
                        <div class="form-group mb-3">
                            <label for="images">Imagens (máximo 5)</label>
                            <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>

                            {{-- Mensagem de erro --}}
                            <small id="imageError" class="text-danger d-none">Você pode selecionar no máximo 5 imagens.</small>

                            {{-- Preview das imagens --}}
                            <div id="imagePreview" class="d-flex flex-wrap gap-2 mt-3"></div>
                        </div>

                        {{-- Referências --}}
                        <div class="form-group mb-4">
                            <label for="info_references">Referências</label>
                            <input type="text" class="form-control" id="info_references" name="info_references">
                        </div>

                        <button type="submit" class="btn btn-success">Salvar Planta</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script de preview, remoção e ordenação --}}
<script>
let selectedImages = [];
const input = document.getElementById('images');
const preview = document.getElementById('imagePreview');
const errorMsg = document.getElementById('imageError');

// Exibe as imagens
// Preview sem limpar o input
function renderPreviews(input) {
    const previewContainer = document.getElementById('imagePreview');
    previewContainer.innerHTML = '';

    for (const file of input.files) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.classList.add('img-thumbnail', 'm-1');
            img.style.width = '100px';
            img.style.height = '100px';
            previewContainer.appendChild(img);
        };
        reader.readAsDataURL(file);
    }
}

// Drag and drop functions
let draggedIndex = null;

function dragStart(e) {
    draggedIndex = e.currentTarget.dataset.index;
    e.dataTransfer.effectAllowed = 'move';
}

function dragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
}

function drop(e) {
    e.preventDefault();
    const targetIndex = e.currentTarget.dataset.index;
    if (draggedIndex === targetIndex) return;

    const draggedItem = selectedImages[draggedIndex];
    selectedImages.splice(draggedIndex, 1);
    selectedImages.splice(targetIndex, 0, draggedItem);
    renderPreviews();
}

// Ao escolher novas imagens
input.addEventListener('change', function(event) {
    const files = Array.from(event.target.files);

    if (selectedImages.length + files.length > 5) {
        errorMsg.classList.remove('d-none');
        input.value = '';
        return;
    } else {
        errorMsg.classList.add('d-none');
    }

    selectedImages = selectedImages.concat(files);
    renderPreviews();
    // input.value = ''; // reseta o input para permitir o reuso
});

// E aí, depois do envio do form:
document.querySelector('form').addEventListener('submit', function () {
    document.getElementById('images').value = ''; // limpa só depois de enviar
});
</script>
@endsection
