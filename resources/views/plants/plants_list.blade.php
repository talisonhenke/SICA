@extends('layouts.main')
@section('content')
<style>
    .plants-container {
        /* max-width: 950px; */
        margin: 0 auto;
        background-color: var(--color-surface-primary);
        /* border-radius: 1rem; */
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        padding: 2rem;
    }

    .plants-title {
        text-align: center;
        font-size: 2rem;
        font-weight: 700;
        color: var(--color-text);
        margin-bottom: 1.5rem;
    }

    /* Barra de pesquisa */
    #searchInput {
        border: 2px solid var(--color-border);
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        font-size: 1.1rem;
        background-color: var(--color-input-bg);
        color: var(--color-input-text);
        transition: all 0.3s ease-in-out;
    }

    #searchInput:focus {
        border-color: var(--color-menu-bg);
        box-shadow: 0 0 0 0.2rem rgba(74,99,63,0.25);
        outline: none;
        background-color: var(--color-surface-primary);
    }

    /* Letra separadora */
    .letter-divider {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--color-menu-bg);
        margin-top: 1.5rem;
        margin-bottom: 0.8rem;
        border-bottom: 2px solid var(--color-bottom-nav-bg);
        padding-bottom: 0.3rem;
    }

    /* Item da planta */
    .plant-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: var(--color-surface-primary);
        color: var(--color-text);
        border: 1px solid var(--color-border);
        border-radius: 0.75rem;
        padding: 0.9rem 1.2rem;
        margin-bottom: 0.6rem;
        transition: all 0.2s ease-in-out;
        text-decoration: none;
    }

    .plant-item:hover {
        background-color: var(--color-surface-secondary);
        color: var(--color-text);
        transform: translateY(-2px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    /* Botões de ação */
    .plant_actions button {
        border: none;
        border-radius: 0.5rem;
        padding: 0.4rem 0.7rem;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .plant_actions .btn-primary {
        background-color: var(--color-menu-bg);
        color: var(--color-surface-primary);
    }

    .plant_actions .btn-primary:hover {
        background-color: var(--color-bottom-nav-bg);
    }

    .plant_actions .btn-danger {
        background-color: var(--color-danger);
        color: var(--color-surface-primary);
    }

    .plant_actions .btn-danger:hover {
        background-color: #b23a35;
    }

    #no-result {
        background-color: var(--color-bg);
        color: var(--color-text);
        border-radius: 0.75rem;
        border: 1px solid var(--color-border);
        padding: 1rem;
        text-align: center;
    }

    /* Botão flutuante */
    .fab-add {
        position: fixed;
        bottom: 80px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: var(--color-menu-bg);
        color: var(--color-surface-primary);
        font-size: 2rem;
        font-weight: 600;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        transition: all 0.2s ease-in-out;
        z-index: 1000;
        text-decoration: none;
    }

    .fab-add:hover {
        background-color: var(--color-bottom-nav-bg);
        transform: scale(1.05);
    }
</style>

<div class="plants-container col-sm-12 col-md-10 col-lg-8">
    <h2 class="plants-title">Lista de Plantas</h2>

    <input type="text" id="searchInput" class="form-control mb-4" placeholder="🔍 Pesquisar planta...">

    <div class="plant-list mt-3" id="plantList">
        <div id="no-result">Não encontrado...</div>
        @php $currentLetter = ''; @endphp
        @foreach ($plants as $plant)
            @php 
                $firstLetter = strtoupper(substr($plant->popular_name, 0, 1));
            @endphp

            {{-- Exibe a letra divisora apenas quando muda --}}
            @if ($firstLetter !== $currentLetter)
                @php $currentLetter = $firstLetter; @endphp
                <div class="letter-divider">{{ $currentLetter }}</div>
            @endif

            @if(Auth::check() && Auth::user()->user_lvl === 'admin')
                <a href="/plant/{{ $plant->id }}/{{ $plant->popular_name }}" class="plant-item">
                    <div>
                        <strong>{{ $plant->popular_name }}</strong>
                        <small>({{ $plant->scientific_name }})</small>
                    </div>
                    <div class="plant_actions d-flex gap-2">
                        <form action="{{ route('plants.edit', $plant->id) }}" method="GET">
                            <button class="btn btn-sm btn-primary" type="submit">Editar</button>
                        </form>
                        <form action="{{ route('plants.destroy', $plant->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta planta?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </div>
                </a>
            @else
                <a href="/plant/{{ $plant->id }}/{{ $plant->popular_name }}" class="plant-item">
                    <div>
                        <strong>{{ $plant->popular_name }}</strong>
                        <small>({{ $plant->scientific_name }})</small>
                    </div>
                </a>
            @endif
        @endforeach
    </div>
</div>

{{-- Botão flutuante para Admin --}}
@if(Auth::check() && Auth::user()->user_lvl === 'admin')
    <a href="{{ route('plants.create') }}" class="fab-add">+</a>
@endif

@endsection

{{-- Script --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $("#no-result").hide();

    $('#searchInput').on('input', function() {
        const searchText = $(this).val().toLowerCase();
        let found = false;

        $('#plantList a').each(function() {
            // Captura apenas o texto do primeiro <div> (nome popular e científico)
            const plantName = $(this).find('div:first').text().toLowerCase();

            if (plantName.includes(searchText)) {
                $(this).css('display', 'flex');
                found = true;
            } else {
                $(this).hide();
            }
        });

        // Oculta ou mostra os divisores de letra conforme a busca
        if (searchText.length > 0) {
            $('.letter-divider').hide();
        } else {
            $('.letter-divider').show();
        }

        $("#no-result").toggle(!found);
    });
});
</script>

