@extends('layouts.main')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 my-4">
            <div class="card">
                <div class="card-header">Editar Planta</div>
                @foreach ($plants as $plant)
                <div class="card-body">
                    <form method="POST" action="{{ route('plants.update', $plant->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <div class="form-group">
                            <label for="scientific_name">Nome Científico</label>
                            <input type="text" class="form-control" id="scientific_name" name="scientific_name" value="{{ old('scientific_name', $plant->scientific_name) }}">
                        </div>

                        <div class="form-group">
                            <label for="popular_name">Nome Popular</label>
                            <input type="text" class="form-control" id="popular_name" name="popular_name" value="{{ old('popular_name', $plant->popular_name) }}">
                        </div>

                        <div class="form-group">
                            <label for="habitat">Habitat</label>
                            <textarea class="form-control" id="habitat" name="habitat" cols="30" rows="5">{{ old('habitat', $plant->habitat) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="useful_parts">Partes utilizadas</label>
                            @php $useful_parts = $plant->useful_parts; @endphp
                            <div class="form-group">
                                <input type="checkbox" name="useful_parts[]" value="Folhas" {{ in_array('Folhas', $useful_parts) ? 'checked' : '' }}> Folhas
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name="useful_parts[]" value="Raízes" {{ in_array('Raízes', $useful_parts) ? 'checked' : '' }}> Raízes
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name="useful_parts[]" value="Sementes" {{ in_array('Sementes', $useful_parts) ? 'checked' : '' }}> Sementes
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name="useful_parts[]" value="Flores" {{ in_array('Flores', $useful_parts) ? 'checked' : '' }}> Flores
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name="useful_parts[]" value="Ramos" {{ in_array('Ramos', $useful_parts) ? 'checked' : '' }}> Ramos
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="characteristics">Características</label>
                            <textarea class="form-control" id="characteristics" name="characteristics" cols="30" rows="5">{{ old('characteristics', $plant->characteristics) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="observations">Observações</label>
                            <textarea class="form-control" id="observations" name="observations" cols="30" rows="5">{{ old('observations', $plant->observations) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="popular_use">Uso popular</label>
                            <textarea class="form-control" id="popular_use" name="popular_use" cols="30" rows="10">{{ old('popular_use', $plant->popular_use) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="chemical_composition">Composição química</label>
                            <input type="text" class="form-control" id="chemical_composition" name="chemical_composition" value="{{ old('chemical_composition', $plant->chemical_composition) }}">
                        </div>

                        <div class="form-group">
                            <label for="contraindications">Contra-indicações</label>
                            <input type="text" class="form-control" id="contraindications" name="contraindications" value="{{ old('contraindications', $plant->contraindications) }}">
                        </div>

                        <div class="form-group">
                            <label for="mode_of_use">Modos de uso</label>
                            <input type="text" class="form-control" id="mode_of_use" name="mode_of_use" value="{{ old('mode_of_use', $plant->mode_of_use) }}">
                        </div>

                        <div class="form-group">
                            <label for="images">Imagem atual:</label>
                            @if($plant->images)
                                <div>
                                    <img src="{{ asset('storage/' . $plant->images) }}" alt="{{ $plant->popular_name }}" style="max-width:200px;">
                                </div>
                            @endif
                            <label for="images">Atualizar imagem</label>
                            <input type="file" class="form-control" id="images" name="images">
                        </div>

                        <div class="form-group">
                            <label for="info_references">Referências</label>
                            <input type="text" class="form-control" id="info_references" name="info_references" value="{{ old('info_references', $plant->info_references) }}">
                        </div>

                        <div class="form-group mb-4">
                            <label for="tags">Tags</label>
                            <input type="text" class="form-control" id="tags" name="tags" value="{{ old('tags', $plant->tags) }}">
                        </div>

                        <button type="submit" class="btn btn-primary">Atualizar</button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
