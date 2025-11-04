@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 my-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">
                    Adicionar Novo Produto
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Planta relacionada --}}
                        <div class="form-group mb-3">
                            <label for="plant_id">Planta relacionada</label>
                            <select class="form-control" id="plant_id" name="plant_id" required>
                                <option value="">Selecione uma planta...</option>
                                @foreach($plants as $plant)
                                    <option value="{{ $plant->id }}">{{ $plant->popular_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Nome do produto --}}
                        <div class="form-group mb-3">
                            <label for="name">Nome do Produto</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        {{-- Descrição --}}
                        <div class="form-group mb-3">
                            <label for="description">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        {{-- Preço --}}
                        <div class="form-group mb-3">
                            <label for="price">Preço (R$)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>

                        {{-- Estoque --}}
                        <div class="form-group mb-3">
                            <label for="stock">Estoque</label>
                            <input type="number" class="form-control" id="stock" name="stock" value="0" required>
                        </div>

                        {{-- Status --}}
                        <div class="form-group mb-3">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="1" selected>Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                        </div>

                        {{-- Imagem do produto --}}
                        <div class="form-group mb-3">
                            <label for="image">Imagem do Produto</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>

                            {{-- Preview --}}
                            <div class="mt-3 text-center">
                                <img id="previewImage" src="#" alt="Preview da imagem" class="img-thumbnail d-none" style="max-width: 250px; height: auto;">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Salvar Produto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script para preview de imagem --}}
<script>
document.getElementById('image').addEventListener('change', function(event) {
    const input = event.target;
    const preview = document.getElementById('previewImage');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        }

        reader.readAsDataURL(input.files[0]);
    }
});
</script>
@endsection
