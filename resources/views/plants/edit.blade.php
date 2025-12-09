@extends('layouts.main')

@section('content')
    <style>
        :root {
            --color-bg: #4A3A2A;
            --color-surface: var(--color-surface-secondary);
            /* fundo dos formulários */
            --color-text: #000000;

            --color-primary: #4A633F;
            --color-primary-dark: #3B5132;
            --color-primary-light: #6B8A55;

            --color-accent: #4A633F;
            --color-secondary: #6B8A55;

            --color-muted: var(--color-border);

            --color-input-bg: #ffffff;
            --color-input-text: #000000;

            --color-danger: #d9534f;
        }

        /* Reuso do estilo do Create */
        .create-plant-container {
            max-width: 850px;
            margin: 3rem auto;
            background-color: var(--color-surface);
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }

        .create-plant-title {
            text-align: center;
            color: var(--color-primary);
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
            background-color: var(--color-input-bg);
            color: var(--color-input-text);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--color-accent);
            box-shadow: 0 0 0 0.2rem rgba(108, 139, 88, 0.25);
            background-color: #fff;
            color: var(--color-text);
        }

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
            border: 2px solid transparent;
            transition: border-color 0.2s, transform 0.2s;
        }

        .preview-item.dragging {
            opacity: 0.6;
            transform: scale(0.95);
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        .remove-btn {
            position: absolute;
            top: 3px;
            right: 3px;
            background-color: var(--color-danger);
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
        }
    </style>

    <div class="create-plant-container">
        <h2 class="create-plant-title">🌿 Editar Planta</h2>

        <form method="POST" action="{{ route('plants.update', $plant->id) }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            {{-- Nome Científico --}}
            <div class="form-group mb-3">
                <label for="scientific_name">Nome Científico</label>
                <input type="text" class="form-control @error('scientific_name') is-invalid @enderror" id="scientific_name"
                    name="scientific_name" value="{{ old('scientific_name', $plant->scientific_name) }}" required>
                @error('scientific_name')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>

            {{-- Nome Popular --}}
            <div class="form-group mb-3">
                <label for="popular_name">Nome Popular</label>
                <input type="text" class="form-control @error('popular_name') is-invalid @enderror" id="popular_name"
                    name="popular_name" value="{{ old('popular_name', $plant->popular_name) }}" required>
                @error('popular_name')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>

            {{-- Habitat --}}
            <div class="form-group mb-3">
                <label for="habitat">Habitat</label>
                <textarea class="form-control @error('habitat') is-invalid @enderror" id="habitat" name="habitat" rows="3"
                    required>{{ old('habitat', $plant->habitat) }}</textarea>
                @error('habitat')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>

            {{-- Partes Utilizadas --}}
            <div class="form-group mb-3">
                <label>Partes Utilizadas</label><br>
                @php
                    $useful_parts = is_array($plant->useful_parts)
                        ? $plant->useful_parts
                        : json_decode($plant->useful_parts ?? '[]', true);
                @endphp
                @foreach (['Folhas', 'Raízes', 'Sementes', 'Flores', 'Ramos'] as $parte)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="useful_parts[]" value="{{ $parte }}"
                            id="parte_{{ $parte }}"
                            {{ in_array($parte, old('useful_parts', $useful_parts)) ? 'checked' : '' }}>
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
                <textarea class="form-control @error('characteristics') is-invalid @enderror" id="characteristics"
                    name="characteristics" rows="3" required>{{ old('characteristics', $plant->characteristics) }}</textarea>
                @error('characteristics')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>

            {{-- Observações --}}
            <div class="form-group mb-3">
                <label for="observations">Observações</label>
                <textarea class="form-control @error('observations') is-invalid @enderror" id="observations" name="observations"
                    rows="3" required>{{ old('observations', $plant->observations) }}</textarea>
                @error('observations')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>

            {{-- Uso Popular --}}
            <div class="form-group mb-3">
                <label for="popular_use">Uso Popular</label>
                <textarea class="form-control @error('popular_use') is-invalid @enderror" id="popular_use" name="popular_use"
                    rows="6" required>{{ old('popular_use', $plant->popular_use) }}</textarea>
                @error('popular_use')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>

            {{-- Composição Química --}}
            <div class="form-group mb-3">
                <label for="chemical_composition">Composição Química</label>
                <input type="text" class="form-control @error('chemical_composition') is-invalid @enderror"
                    id="chemical_composition" name="chemical_composition"
                    value="{{ old('chemical_composition', $plant->chemical_composition) }}" required>
                @error('chemical_composition')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>

            {{-- Contraindicações --}}
            <div class="form-group mb-3">
                <label for="contraindications">Contraindicações</label>
                <input type="text" class="form-control @error('contraindications') is-invalid @enderror"
                    id="contraindications" name="contraindications"
                    value="{{ old('contraindications', $plant->contraindications) }}" required>
                @error('contraindications')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>

            {{-- Modos de Uso --}}
            <div class="form-group mb-3">
                <label for="mode_of_use">Modos de Uso</label>
                <input type="text" class="form-control @error('mode_of_use') is-invalid @enderror" id="mode_of_use"
                    name="mode_of_use" value="{{ old('mode_of_use', $plant->mode_of_use) }}" required>
                @error('mode_of_use')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>

            {{-- QR Code --}}
            <div class="form-group mb-3">
                <label for="qr_code">QR Code (opcional)</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="qr_code" name="qr_code"
                        value="{{ old('qr_code', $plant->qr_code) }}">
                    <button type="button" class="btn btn-outline-primary" id="generateQrBtn">
                        Gerar QR Code Automático
                    </button>
                </div>
                <small class="text-muted">Clique no botão para gerar automaticamente o link de QR Code.</small>
            </div>

            {{-- Imagens --}}
            <div class="form-group mb-3">
                <label for="images" class="file-label">📸 Atualizar Imagens (máximo 5)</label>
                <input type="file" class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" id="images" name="images[]" accept="image/*" multiple required>
                @error('images')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror

                @error('images.*')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
                <small id="imageError" class="invalid-feedback d-none">Você pode selecionar no máximo 5 imagens.</small>
                <div id="imagePreview" class="image-preview-container">
                    @php
                        $existingImages = json_decode($plant->images ?? '[]', true);
                    @endphp
                    @foreach ($existingImages as $image)
                        <div class="preview-item existing-image" data-file="{{ $image }}">
                            <img src="{{ asset($image) }}" alt="Imagem da planta">
                            <button type="button" class="remove-btn remove-existing">×</button>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="deleted_images" id="deletedImagesInput">
                <input type="hidden" name="ordered_images" id="orderedImagesInput">
            </div>

            {{-- Referências --}}
            <div class="form-group mb-3">
                <label for="info_references">Referências</label>
                <input type="text" class="form-control @error('info_references') is-invalid @enderror"
                    id="info_references" name="info_references"
                    value="{{ old('info_references', $plant->info_references) }}" required>
                @error('info_references')
                    <small class="invalid-feedback">{{ $message }}</small>
                @enderror
            </div>

            {{-- BOTÃO PARA ABRIR MODAL DE TAGS --}}
            <div class="form-group mb-3">
                <label>Tags</label>
                <div>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#tagsModal">
                        Adicionar / Remover Tags
                    </button>
                </div>
                {{-- Lista das tags selecionadas --}}
                <div class="mt-2" id="selectedTagsContainer">
                    @php
                        $plantTagIds = $plant->tags->pluck('id')->toArray(); // IDs das tags já associadas
                    @endphp
                    @foreach ($plant->tags as $tag)
                        <span class="badge bg-success me-1">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>

            {{-- MODAL DE TAGS --}}
            <div class="modal fade" id="tagsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Selecionar Tags</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @foreach ($tags as $tag)
                                <div class="form-check">
                                    <input class="form-check-input tag-checkbox" type="checkbox"
                                        value="{{ $tag->id }}" id="tag_{{ $tag->id }}"
                                        {{ in_array($tag->id, $plantTagIds) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tag_{{ $tag->id }}">
                                        {{ $tag->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            <button type="button" class="btn btn-primary" id="saveTagsBtn">Salvar Tags</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- INPUT HIDDEN PARA ENVIAR TAGS SELECIONADAS --}}
            <input type="hidden" name="tags" id="tagsInput" value="{{ implode(',', $plantTagIds) }}">

            <button type="submit" class="btn-submit">💾 Salvar Alterações</button>
            <a href="{{ route('plants.index') }}" class="btn-cancel">❌ Cancelar</a>
        </form>
    </div>

    {{-- SCRIPT PARA SINCRONIZAR CHECKBOX DE TAGS COM O INPUT HIDDEN --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const saveBtn = document.getElementById('saveTagsBtn');
            const tagsInput = document.getElementById('tagsInput');
            const selectedContainer = document.getElementById('selectedTagsContainer');

            saveBtn.addEventListener('click', function() {
                const selected = Array.from(document.querySelectorAll('.tag-checkbox:checked')).map(cb => cb
                    .value);
                tagsInput.value = selected.join(',');

                // Atualiza a visualização das tags selecionadas
                selectedContainer.innerHTML = '';
                Array.from(document.querySelectorAll('.tag-checkbox:checked')).forEach(cb => {
                    const label = document.querySelector(`label[for="${cb.id}"]`).innerText;
                    const span = document.createElement('span');
                    span.className = 'badge bg-success me-1';
                    span.innerText = label;
                    selectedContainer.appendChild(span);
                });

                // Fecha o modal
                const modalEl = document.getElementById('tagsModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
            });
        });
    </script>

    {{-- Scripts (iguais ao Create, com suporte a imagens existentes e novas) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('images');
            const preview = document.getElementById('imagePreview');
            const errorMsg = document.getElementById('imageError');
            const deletedImagesInput = document.getElementById('deletedImagesInput');
            const orderedImagesInput = document.getElementById('orderedImagesInput');

            let selectedImages = [];
            let existingImages = Array.from(document.querySelectorAll('.existing-image')).map(el => el.dataset
                .file);
            let deletedImages = [];
            let draggedIndex = null;

            function updateHiddenInputs() {
                deletedImagesInput.value = JSON.stringify(deletedImages);
                orderedImagesInput.value = JSON.stringify(existingImages);
            }

            function renderPreviews() {
                preview.innerHTML = '';
                existingImages.forEach((file, index) => {
                    const div = document.createElement('div');
                    div.className = 'preview-item existing-image';
                    div.dataset.index = index;
                    div.dataset.file = file;
                    div.draggable = true;

                    const img = document.createElement('img');
                    img.src = `/${file}`;
                    div.appendChild(img);

                    const remove = document.createElement('button');
                    remove.className = 'remove-btn';
                    remove.innerHTML = '×';
                    remove.addEventListener('click', () => {
                        deletedImages.push(file);
                        existingImages.splice(index, 1);
                        renderPreviews();
                        updateHiddenInputs();
                    });

                    div.addEventListener('dragstart', () => draggedIndex = index);
                    div.addEventListener('dragover', e => e.preventDefault());
                    div.addEventListener('drop', e => {
                        e.preventDefault();
                        const targetIndex = Number(div.dataset.index);
                        const dragged = existingImages[draggedIndex];
                        existingImages.splice(draggedIndex, 1);
                        existingImages.splice(targetIndex, 0, dragged);
                        renderPreviews();
                        updateHiddenInputs();
                    });

                    div.appendChild(remove);
                    preview.appendChild(div);
                });
                selectedImages.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const div = document.createElement('div');
                        div.className = 'preview-item';
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        div.appendChild(img);
                        const remove = document.createElement('button');
                        remove.className = 'remove-btn';
                        remove.innerHTML = '×';
                        remove.addEventListener('click', () => {
                            selectedImages.splice(index, 1);
                            renderPreviews();
                            updateHiddenInputs();
                        });
                        div.appendChild(remove);
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }

            input.addEventListener('change', e => {
                const files = Array.from(e.target.files);
                if (existingImages.length + selectedImages.length + files.length > 5) {
                    errorMsg.classList.remove('d-none');
                    return;
                }
                errorMsg.classList.add('d-none');
                selectedImages = selectedImages.concat(files);
                renderPreviews();
                updateHiddenInputs();
            });

            document.getElementById('generateQrBtn').addEventListener('click', function() {
                const baseUrl = window.location.origin;
                const plantId = {{ $plant->id }};
                const slug = @json($plant->slug);
                const qr = `${baseUrl}/plant/${plantId}/${slug}`;
                const inputQr = document.getElementById('qr_code');
                inputQr.value = qr;
                inputQr.classList.add('border-success');
                setTimeout(() => inputQr.classList.remove('border-success'), 1000);
            });

            renderPreviews();
            updateHiddenInputs();
        });
    </script>
@endsection
