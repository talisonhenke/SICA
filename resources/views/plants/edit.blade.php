@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 my-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">
                    Editar Planta
                </div>

                <div class="card-body">
                    @foreach ($plants as $plant)
                    <form method="POST" action="{{ route('plants.update', $plant->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- deve ser PUT, não POST --}}

                        {{-- Nome científico --}}
                        <div class="form-group mb-3">
                            <label for="scientific_name">Nome Científico</label>
                            <input type="text" class="form-control" id="scientific_name" name="scientific_name" 
                                   value="{{ old('scientific_name', $plant->scientific_name) }}" required>
                        </div>

                        {{-- Nome popular --}}
                        <div class="form-group mb-3">
                            <label for="popular_name">Nome Popular</label>
                            <input type="text" class="form-control" id="popular_name" name="popular_name" 
                                   value="{{ old('popular_name', $plant->popular_name) }}" required>
                        </div>

                        {{-- Habitat --}}
                        <div class="form-group mb-3">
                            <label for="habitat">Habitat</label>
                            <textarea class="form-control" id="habitat" name="habitat" rows="3" required>{{ old('habitat', $plant->habitat) }}</textarea>
                        </div>

                        {{-- Partes utilizadas --}}
                        <div class="form-group mb-3">
                            <label>Partes Utilizadas</label><br>
                            @php 
                                $useful_parts = is_array($plant->useful_parts) ? $plant->useful_parts : json_decode($plant->useful_parts ?? '[]', true);
                            @endphp
                            @foreach(['Folhas', 'Raízes', 'Sementes', 'Flores', 'Ramos'] as $parte)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="useful_parts[]" 
                                           value="{{ $parte }}" id="parte_{{ $parte }}"
                                           {{ in_array($parte, $useful_parts) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="parte_{{ $parte }}">{{ $parte }}</label>
                                </div>
                            @endforeach
                        </div>

                        {{-- Características --}}
                        <div class="form-group mb-3">
                            <label for="characteristics">Características</label>
                            <textarea class="form-control" id="characteristics" name="characteristics" rows="3">{{ old('characteristics', $plant->characteristics) }}</textarea>
                        </div>

                        {{-- Observações --}}
                        <div class="form-group mb-3">
                            <label for="observations">Observações</label>
                            <textarea class="form-control" id="observations" name="observations" rows="3">{{ old('observations', $plant->observations) }}</textarea>
                        </div>

                        {{-- Uso popular --}}
                        <div class="form-group mb-3">
                            <label for="popular_use">Uso Popular</label>
                            <textarea class="form-control" id="popular_use" name="popular_use" rows="6">{{ old('popular_use', $plant->popular_use) }}</textarea>
                        </div>

                        {{-- Composição química --}}
                        <div class="form-group mb-3">
                            <label for="chemical_composition">Composição Química</label>
                            <input type="text" class="form-control" id="chemical_composition" 
                                   name="chemical_composition" value="{{ old('chemical_composition', $plant->chemical_composition) }}">
                        </div>

                        {{-- Contraindicações --}}
                        <div class="form-group mb-3">
                            <label for="contraindications">Contraindicações</label>
                            <input type="text" class="form-control" id="contraindications" 
                                   name="contraindications" value="{{ old('contraindications', $plant->contraindications) }}">
                        </div>

                        {{-- Modo de uso --}}
                        <div class="form-group mb-3">
                            <label for="mode_of_use">Modos de Uso</label>
                            <input type="text" class="form-control" id="mode_of_use" 
                                   name="mode_of_use" value="{{ old('mode_of_use', $plant->mode_of_use) }}">
                        </div>

                        {{-- QR Code --}}
                        <div class="form-group mb-3">
                            <label for="qr_code">QR Code (opcional)</label>
                            <input type="text" class="form-control" id="qr_code" 
                                   name="qr_code" value="{{ old('qr_code', $plant->qr_code) }}">
                        </div>

                        {{-- Imagens --}}
                        <div class="form-group mb-3">
                            <label>Imagens (máximo 5)</label>
                            <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
                            <small id="imageError" class="text-danger d-none">Você pode selecionar no máximo 5 imagens.</small>

                            {{-- Pré-visualização das imagens --}}
                            <div id="imagePreview" class="d-flex flex-wrap gap-2 mt-3">
                                @php
                                    $existingImages = json_decode($plant->images ?? '[]', true);
                                @endphp
                                @foreach($existingImages as $image)
                                    <div class="position-relative existing-image" data-file="{{ $image }}">
                                        <img src="{{ asset($image) }}" 
                                             class="rounded border" style="width:120px; height:120px; object-fit:cover;">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute remove-existing" 
                                                style="top:0; right:0; border-radius:50%;">×</button>
                                    </div>
                                @endforeach
                            </div>

                            <input type="hidden" name="deleted_images" id="deletedImagesInput">
                            <input type="hidden" name="ordered_images" id="orderedImagesInput">
                        </div>

                        {{-- Referências --}}
                        <div class="form-group mb-4">
                            <label for="info_references">Referências</label>
                            <input type="text" class="form-control" id="info_references" 
                                   name="info_references" value="{{ old('info_references', $plant->info_references) }}">
                        </div>

                        <button type="submit" class="btn btn-success">Salvar Alterações</button>
                    </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script de preview, exclusão e ordenação --}}
<script>
let selectedImages = [];
let existingImages = Array.from(document.querySelectorAll('.existing-image')).map(el => el.dataset.file);
let deletedImages = [];

const input = document.getElementById('images');
const preview = document.getElementById('imagePreview');
const errorMsg = document.getElementById('imageError');

// Campos ocultos no formulário
const deletedImagesInput = document.getElementById('deletedImagesInput');
const orderedImagesInput = document.getElementById('orderedImagesInput');

// Atualiza os inputs ocultos com as informações mais recentes
function updateHiddenInputs() {
    if (deletedImagesInput) {
        deletedImagesInput.value = JSON.stringify(deletedImages);
    }
    if (orderedImagesInput) {
        orderedImagesInput.value = JSON.stringify([...existingImages]);
    }
}

// Renderiza as imagens atuais e novas
function renderPreviews() {
    preview.innerHTML = '';

    // === IMAGENS EXISTENTES ===
    existingImages.forEach((file, index) => {
        const imgContainer = document.createElement('div');
        imgContainer.classList.add('position-relative', 'existing-image');
        imgContainer.dataset.index = index;
        imgContainer.dataset.file = file;
        imgContainer.draggable = true;

        const img = document.createElement('img');
        img.src = `/${file}`;
        img.classList.add('rounded', 'border');
        img.style.width = '120px';
        img.style.height = '120px';
        img.style.objectFit = 'cover';

        const removeBtn = document.createElement('button');
        removeBtn.innerHTML = '×';
        removeBtn.type = 'button';
        removeBtn.classList.add('btn', 'btn-sm', 'btn-danger', 'position-absolute');
        removeBtn.style.top = '0';
        removeBtn.style.right = '0';
        removeBtn.style.borderRadius = '50%';
        removeBtn.addEventListener('click', () => {
            deletedImages.push(file);
            existingImages.splice(index, 1);
            renderPreviews();
            updateHiddenInputs();
        });

        imgContainer.addEventListener('dragstart', dragStart);
        imgContainer.addEventListener('dragover', dragOver);
        imgContainer.addEventListener('drop', drop);

        imgContainer.appendChild(img);
        imgContainer.appendChild(removeBtn);
        preview.appendChild(imgContainer);
    });

    // === NOVAS IMAGENS ===
    selectedImages.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = e => {
            const imgContainer = document.createElement('div');
            imgContainer.classList.add('position-relative');
            imgContainer.draggable = true;

            const img = document.createElement('img');
            img.src = e.target.result;
            img.classList.add('rounded', 'border');
            img.style.width = '120px';
            img.style.height = '120px';
            img.style.objectFit = 'cover';

            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = '×';
            removeBtn.type = 'button';
            removeBtn.classList.add('btn', 'btn-sm', 'btn-danger', 'position-absolute');
            removeBtn.style.top = '0';
            removeBtn.style.right = '0';
            removeBtn.style.borderRadius = '50%';
            removeBtn.addEventListener('click', () => {
                selectedImages.splice(index, 1);
                renderPreviews();
                updateHiddenInputs();
            });

            imgContainer.appendChild(img);
            imgContainer.appendChild(removeBtn);
            preview.appendChild(imgContainer);
        };
        reader.readAsDataURL(file);
    });
}

// === Drag & Drop para reordenar ===
let draggedIndex = null;

function dragStart(e) {
    draggedIndex = e.currentTarget.dataset.index;
}

function dragOver(e) {
    e.preventDefault();
}

function drop(e) {
    e.preventDefault();
    const targetIndex = e.currentTarget.dataset.index;
    const temp = existingImages[draggedIndex];
    existingImages.splice(draggedIndex, 1);
    existingImages.splice(targetIndex, 0, temp);
    renderPreviews();
    updateHiddenInputs();
}

// === Ao adicionar novas imagens ===
input.addEventListener('change', function(event) {
    const files = Array.from(event.target.files);
    if (existingImages.length + selectedImages.length + files.length > 5) {
        errorMsg.classList.remove('d-none');
        return;
    }

    errorMsg.classList.add('d-none');
    selectedImages = selectedImages.concat(files);
    renderPreviews();
    updateHiddenInputs();
    // input.value = ''; // Mantém comentado, como você fez
});

// === Inicializa preview e inputs ocultos ao carregar ===
renderPreviews();
updateHiddenInputs();
</script>

@endsection
