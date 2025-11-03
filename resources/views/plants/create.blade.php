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
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="manual_qr_toggle">
                                <label class="form-check-label fw-semibold" for="manual_qr_toggle">
                                    Definir QR Code manualmente
                                </label>
                            </div>

                            <div id="qr_code_wrapper" class="d-none">
                                <label for="qr_code">QR Code (opcional)</label>
                                <input type="text" class="form-control" id="qr_code" name="qr_code" placeholder="Link ou identificador do QR Code manual">
                                <small class="text-muted">Se não preencher, o QR Code será gerado automaticamente.</small>
                            </div>
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

<script>
    //Comportamento do campo QR-code
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('manual_qr_toggle');
    const qrWrapper = document.getElementById('qr_code_wrapper');
    const qrInput = document.getElementById('qr_code');

    // Esconde inicialmente
    qrWrapper.classList.add('d-none');
    qrInput.disabled = true;

    toggle.addEventListener('change', function () {
        if (this.checked) {
            qrWrapper.classList.remove('d-none');
            qrInput.disabled = false;
        } else {
            qrWrapper.classList.add('d-none');
            qrInput.disabled = true;
            qrInput.value = ''; // limpa o valor se o usuário desmarcar
        }
    });
});
</script>

{{-- Script de preview, remoção e ordenação --}}
<script>
/* ---- Configuração inicial ---- */
let selectedImages = []; // array de File
const input = document.getElementById('images');
const preview = document.getElementById('imagePreview');
const errorMsg = document.getElementById('imageError');
const form = document.querySelector('form');

/* ---- Renderiza previews (existente apenas em create: só selectedImages) ---- */
function renderPreviews() {
    preview.innerHTML = '';

    selectedImages.forEach((file, index) => {
        const imgContainer = document.createElement('div');
        imgContainer.className = 'position-relative d-inline-block m-1';
        imgContainer.style.width = '120px';
        imgContainer.style.height = '120px';
        imgContainer.style.cursor = 'grab';
        imgContainer.draggable = true;
        imgContainer.dataset.index = index;

        // events drag
        imgContainer.addEventListener('dragstart', dragStart);
        imgContainer.addEventListener('dragover', dragOver);
        imgContainer.addEventListener('drop', drop);

        const img = document.createElement('img');
        img.className = 'rounded border';
        img.style.width = '120px';
        img.style.height = '120px';
        img.style.objectFit = 'cover';

        const reader = new FileReader();
        reader.onload = e => img.src = e.target.result;
        reader.readAsDataURL(file);

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.innerHTML = '×';
        removeBtn.className = 'btn btn-sm btn-danger position-absolute';
        removeBtn.style.top = '2px';
        removeBtn.style.right = '2px';
        removeBtn.style.borderRadius = '50%';
        removeBtn.addEventListener('click', () => {
            selectedImages.splice(index, 1);
            renderPreviews();
        });

        imgContainer.appendChild(img);
        imgContainer.appendChild(removeBtn);
        preview.appendChild(imgContainer);
    });
}

/* ---- Drag & Drop handlers ---- */
let draggedIndex = null;

function dragStart(e) {
    draggedIndex = Number(e.currentTarget.dataset.index);
    // para visual feedback
    e.dataTransfer.effectAllowed = 'move';
}

function dragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
}

function drop(e) {
    e.preventDefault();
    const targetIndex = Number(e.currentTarget.dataset.index);
    if (draggedIndex === null || targetIndex === draggedIndex) return;

    // Reordena selectedImages
    const draggedItem = selectedImages[draggedIndex];
    selectedImages.splice(draggedIndex, 1);
    selectedImages.splice(targetIndex, 0, draggedItem);

    // Limpa draggedIndex e redesenha
    draggedIndex = null;
    renderPreviews();
}

/* ---- Ao selecionar novos arquivos ---- */
input.addEventListener('change', function(event) {
    const files = Array.from(event.target.files);

    // valida limite
    if (selectedImages.length + files.length > 5) {
        if (errorMsg) errorMsg.classList.remove('d-none');
        // não adiciona os arquivos excedentes
        return;
    } else {
        if (errorMsg) errorMsg.classList.add('d-none');
    }

    // adiciona ao array
    selectedImages = selectedImages.concat(files);
    renderPreviews();

    // NÃO limpamos input aqui; vamos reconstruí-lo no submit via DataTransfer
});

/* ---- Antes do submit: substituir input.files pela ordem escolhida ---- */
form.addEventListener('submit', function(e) {
    // Se não houver imagens, nada a fazer
    if (selectedImages.length === 0) {
        // deixa o input como está (pode estar vazio)
        return;
    }

    // Cria um DataTransfer e adiciona os arquivos na ordem desejada
    const dt = new DataTransfer();
    selectedImages.forEach(file => {
        dt.items.add(file);
    });

    // Associa ao input (isso substituirá o filelist original)
    input.files = dt.files;

    // O formulário seguirá e enviará os arquivos na ordem definida
});
</script>


@endsection
