@extends('layouts.main')

@section('content')

<style>
    .product-card {
        background: var(--color-surface-primary);
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,0.08);
    }

    .product-header {
        background: var(--color-primary);
        color: #fff;
        padding: 12px 18px;
        font-weight: 600;
        border-radius: 12px 12px 0 0;
    }

    .form-label {
        font-weight: 600;
        color: var(--color-text);
    }

    .form-control, .form-select {
        background: var(--color-surface-secondary);
        border: 1px solid rgba(0,0,0,0.15);
        color: var(--color-text);
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 0.15rem rgba(94,74,59,0.3);
    }

    .invalid-feedback {
        font-size: .875rem;
        color: #b30000;
    }

    #previewImage {
        max-width: 260px;
        border-radius: 10px;
        border: 2px solid var(--color-primary-light);
    }

    .btn-save {
        background: var(--color-secondary);
        border: none;
        font-weight: 600;
    }

    .btn-save:hover {
        background: var(--color-accent);
    }
</style>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="product-card shadow-sm">
                <div class="product-header">
                    Editar Produto
                </div>

                <div class="card-body p-4">

                    <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Planta relacionada --}}
                        <div class="mb-3">
                            <label for="plant_id" class="form-label">Planta relacionada</label>
                            <select class="form-select @error('plant_id') is-invalid @enderror"
                                    id="plant_id" name="plant_id">
                                <option value="">Selecione...</option>

                                @foreach($plants as $plant)
                                    <option value="{{ $plant->id }}"
                                        {{ old('plant_id', $product->plant_id) == $plant->id ? 'selected' : '' }}>
                                        {{ $plant->popular_name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('plant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nome --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Produto</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name"
                                   value="{{ old('name', $product->name) }}">

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Descrição --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>

                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Preço --}}
                        <div class="mb-3">
                            <label for="price" class="form-label">Preço (R$)</label>
                            <input type="number" step="0.01"
                                   class="form-control @error('price') is-invalid @enderror"
                                   id="price" name="price"
                                   value="{{ old('price', $product->price) }}">

                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Estoque --}}
                        <div class="mb-3">
                            <label for="stock" class="form-label">Estoque</label>
                            <input type="number"
                                   class="form-control @error('stock') is-invalid @enderror"
                                   id="stock" name="stock"
                                   value="{{ old('stock', $product->stock) }}">

                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status">
                                <option value="1" {{ old('status', $product->status) == 1 ? 'selected' : '' }}>Ativo</option>
                                <option value="0" {{ old('status', $product->status) == 0 ? 'selected' : '' }}>Inativo</option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Imagem --}}
                        <div class="mb-3">
                            <label for="image" class="form-label">Imagem do Produto</label>
                            <input type="file"
                                   class="form-control @error('image') is-invalid @enderror"
                                   id="image" name="image"
                                   accept="image/*">

                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="text-center mt-3">
                                <img id="previewImage"
                                     src="{{ asset($product->image) }}"
                                     alt="Imagem atual"
                                     class="img-thumbnail"
                                     style="max-width: 260px;">
                            </div>
                        </div>

                        {{-- Botão --}}
                        <div class="text-end">
                            <button type="submit" class="btn btn-save px-4 py-2">
                                Atualizar Produto
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- Preview script --}}
<script>
document.getElementById('image').addEventListener('change', function(event) {
    const input = event.target;
    const preview = document.getElementById('previewImage');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
        }

        reader.readAsDataURL(input.files[0]);
    }
});
</script>

@endsection
