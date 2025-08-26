@extends('layouts.main')
@section('content')
<div class="container">
    <input type="text" id="searchInput" class="form-control mb-3 mt-4" placeholder="Pesquisar...">
    <div class="mx-auto col-sm-12 col-md-12 col-lg-12 col-xl-10 border-top border-white py-4 overflow-hidden">
        {{-- Listagem de platas sem botões (sem editar e excluir) --}}
        {{-- <div class="list-group" id="plantList"> <div id="no-result" class="list-group-item list-group-item-action text-center">Não encontrado...</div> @foreach ($plants as $plant) <a href="/plant/{{ $plant->id }}/{{ $plant->popular_name }}" class="list-group-item list-group-item-action text-center">{{ $plant->popular_name }} ({{ $plant->scientific_name }})</a> @endforeach </div> --}}
        
        <div class="list-group" id="plantList">
        <div id="no-result" class="list-group-item list-group-item-action text-center">Não encontrado...</div>
        @foreach ($plants as $plant)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <a href="/plant/{{ $plant->id }}/{{ $plant->popular_name }}" class="text-decoration-none">
                    {{ $plant->popular_name }} ({{ $plant->scientific_name }})
                </a>
                <a href="{{ route('plants.edit', $plant->id) }}" class="btn btn-sm btn-primary">Editar</a>
            </div>
        @endforeach
    </div>
    </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#no-result").hide();
        $('#searchInput').on('input', function() {
            var searchText = $(this).val().toLowerCase();
            $('#plantList a').each(function() {
                var plantName = $(this).text().toLowerCase();
                if (plantName.includes(searchText)) {
                    $(this).show();
                    $("#no-result").hide();
                } else {
                    $(this).hide();
                    $("#no-result").show();

                }
            });
        });
    });
</script>
