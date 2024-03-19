@extends('layouts.main')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 my-4">
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
                            <div class="form-group">
                                <label for="useful_parts">Partes utilizadas</label>
                                <input type="text" class="form-control" id="useful_parts" name="useful_parts">
                            </div>
                            <div class="form-group">
                                <label for="characteristics">Características</label>
                                <input type="text" class="form-control" id="characteristics" name="characteristics">
                            </div>
                            <div class="form-group">
                                <label for="observations">Observações</label>
                                <input type="text" class="form-control" id="observations" name="observations">
                            </div>
                            <div class="form-group">
                                <label for="popular_use">Uso popular</label>
                                <input type="text" class="form-control" id="popular_use" name="popular_use">
                            </div>
                            <div class="form-group">
                                <label for="chemical_composition">Composição química</label>
                                <input type="text" class="form-control" id="chemical_composition" name="chemical_composition">
                            </div>
                            <div class="form-group">
                                <label for="contraindications">Contra-indicações</label>
                                <input type="text" class="form-control" id="contraindications" name="contraindications">
                            </div>
                            <div class="form-group">
                                <label for="mode_of_use">Modos de uso</label>
                                <input type="text" class="form-control" id="mode_of_use" name="mode_of_use">
                            </div>
                            <div class="form-group">
                                <label for="images">Imagens</label>
                                <input type="text" class="form-control" id="images" name="images">
                            </div>
                            <div class="form-group">
                                <label for="info_references">Referências</label>
                                <input type="text" class="form-control" id="info_references" name="info_references">
                            </div>
                            <div class="form-group">
                                <label for="tags">Tags</label>
                                <input type="text" class="form-control" id="tags" name="tags">
                            </div>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection