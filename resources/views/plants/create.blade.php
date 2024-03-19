@extends('layouts.main')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Adicionar Nova Planta</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('plants.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="scientific_name">Nome Científico</label>
                                <input type="text" class="form-control" id="scientific_name" name="scientific_name">
                            </div>
                            <div class="form-group">
                                <label for="popular_name">Nome Popular</label>
                                <input type="text" class="form-control" id="popular_name" name="popular_name">
                            </div>
                            <div class="form-group">
                                <label for="habitat">Habitat</label>
                                <input type="text" class="form-control" id="habitat" name="habitat">
                            </div>
                            <!-- Adicione os campos restantes do formulário conforme necessário -->
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection