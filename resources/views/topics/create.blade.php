@extends('layouts.main')

@section('content')
<div class="container">
    <h2>Criar novo Tópico</h2>
    <form action="{{ route('topics.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group mb-3">
            <label for="title">Título</label>
            <input type="text" class="form-control" name="title" required>
        </div>

        <div class="form-group mb-3">
            <label for="description">Descrição</label>
            <textarea class="form-control" name="description" rows="3" required></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="content">Conteúdo</label>
            <textarea class="form-control" name="content" rows="5" required></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="image">Imagem do Tópico</label>
            <input type="file" class="form-control" name="image" accept="image/*" required>
        </div>

        <button type="submit" class="btn btn-success">Salvar Tópico</button>
    </form>
</div>
@endsection
