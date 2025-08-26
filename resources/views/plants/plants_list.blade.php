@extends('layouts.main')
@section('content')
<div class="container">
    <input type="text" id="searchInput" class="form-control mb-3 mt-4" placeholder="Pesquisar...">
    <div class="mx-auto col-sm-12 col-md-12 col-lg-12 col-xl-10 border-top border-white py-4 overflow-hidden">
        <div class="list-group" id="plantList">
            <div id="no-result" class="list-group-item list-group-item-action text-center">Não encontrado...</div>
            @foreach ($plants as $plant)
                <a href="#" class="list-group-item list-group-item-action justify-content-between align-items-center">{{ $plant->popular_name }} ({{ $plant->scientific_name }})
                    <div class="plant_actions d-flex gap-2">
                        <form action="{{ route('plants.edit', $plant->id) }}" method="GET">
                            <button class="btn btn-sm btn-primary" type="submit">Editar</button>
                        </form>
                        <form action="{{ route('plants.destroy', $plant->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este registro?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#no-result").hide();
        $('#plantList a').each(function() {
            $(this).css('display', 'flex'); // Força o flex no carregamento
            // $(this).css('align-items', 'center'); // Opcional, para alinhar verticalmente
            // $(this).css('justify-content', 'space-between'); // Espaço entre nome e botões
        });

        $('#searchInput').on('input', function() {
            var searchText = $(this).val().toLowerCase();
            var found = false;
            $('#plantList a').each(function() {
                var plantName = $(this).text().toLowerCase();
                if (plantName.includes(searchText)) {
                    // $(this).show();
                    $(this).css('display', 'flex'); // Mantém o flex
                    found = true;
                } else {
                    $(this).hide();
                }
            });
            if (found){
                $("#no-result").hide();
            }
            else{
                $("#no-result").show();
            }
        });
    });
</script>