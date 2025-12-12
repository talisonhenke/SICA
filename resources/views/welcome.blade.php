@extends('layouts.main')
@section('content')

<style>
    body {
        background-color: var(--color-bg);
        color: var(--color-text);
    }

    /* Campo de busca */
    #homeSearchInput {
        border: 1px solid var(--color-border);
        background-color: var(--color-input-bg);
        color: var(--color-input-text);
        border-radius: 0.6rem;
        transition: all 0.3s ease;
    }

    #homeSearchInput::placeholder {
        color: var(--color-muted);
    }

    #homeSearchInput:focus {
        border-color: var(--color-secondary);
        box-shadow: 0 0 0 0.2rem rgba(76, 99, 63, 0.25);
        outline: none;
    }

    /* Botão de busca */
    #searchButton {
        background-color: var(--color-secondary);
        border: none;
        color: #fff;
        transition: background-color 0.3s ease, transform 0.1s ease;
    }

    #searchButton:hover {
        background-color: var(--color-accent);
        transform: translateY(-1px);
    }

    #searchButton:active {
        transform: translateY(0);
    }

    /* Modal de resultados */
    .modal-content {
        background-color: var(--color-surface-primary) !important;
        color: var(--color-text);
        border-radius: 1rem;
        border: 1px solid var(--color-border);
    }

    .modal-header {
        background-color: var(--color-secondary);
        color: #fff;
        border-bottom: none;
    }

    .modal-title {
        font-weight: 600;
    }

    .list-group-item {
        background-color: var(--color-surface);
        border-color: var(--color-border);
        color: var(--color-text);
    }

    .list-group-item:hover {
        background-color: var(--color-surface-secondary);
    }

    /* Cards dos tópicos */
    .card {
        background-color: var(--color-surface-primary);
        border: 1px solid var(--color-border);
        border-radius: 1rem;
        transition: transform 0.2s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .card-title {
        color: var(--color-secondary);
    }

    .card-text {
        color: var(--color-muted);
    }

    /* Botão "Ler mais" */
    .btn-outline-success {
        color: var(--color-secondary);
        border-color: var(--color-secondary);
        transition: all 0.3s ease;
        font-weight: 600;
        border-radius: 0.6rem;
    }

    .btn-outline-success:hover {
        background-color: var(--color-secondary);
        color: #fff;
        border-color: var(--color-secondary);
    }

    /* Seção informativa (Fitoterapia) */
    .content {
        background-color: var(--color-surface-primary);
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.10);
    }

    .contentTitle {
        color: var(--color-secondary);
        font-weight: 700;
    }

    .personalSiteText p, .companySiteText p {
        color: var(--color-text);
        line-height: 1.7;
    }

    /* Links da lista */
    a.text-dark {
        color: var(--color-text) !important;
    }

    a.text-dark:hover {
        color: var(--color-secondary) !important;
    }

    .tag-tooltip {
    position: relative;
    display: inline-block;
}

.tooltip-box {
    display: none;
    position: absolute;
    top: 26px;
    left: 0;
    z-index: 9999;
    background: #fff;
    padding: 10px;
    width: 220px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    font-size: 0.85rem;
}

.tag-tooltip:hover .tooltip-box {
    display: block;
}

.search-dropdown {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    width: 100%;
    max-height: 360px;
    overflow-y: auto;
    z-index: 1050;

    background-color: var(--color-surface-primary);
    border: 1px solid var(--color-border);
    border-radius: 0.75rem;
}

/* Remove borda duplicada nos itens */
.search-dropdown .list-group-item {
    border-left: none;
    border-right: none;
}

.search-dropdown .list-group-item:first-child {
    border-top: none;
}

.search-dropdown .list-group-item:last-child {
    border-bottom: none;
}


</style>

{{-- Seção principal com busca --}}
<div class="mx-auto col-sm-12 col-md-8 col-lg-6">
    <div class="position-relative">

        <div class="input-group mt-4">
            <input type="text"
                   id="homeSearchInput"
                   class="form-control form-control-lg"
                   placeholder="Digite o nome da planta...">

            <button class="btn btn-lg" type="button" id="searchButton">
                <i class="bi bi-search"></i> Buscar
            </button>
        </div>

        <!-- DROPDOWN -->
        <div id="searchDropdown"
             class="list-group shadow d-none search-dropdown">
            <div class="list-group-item text-muted text-center">
                Digite algo para buscar...
            </div>
        </div>

    </div>
</div>



{{-- Modal para exibir resultados --}}
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="searchModalLabel">Resultados da busca</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group" id="searchResults">
          <li class="list-group-item text-center text-muted">Digite algo para buscar...</li>
        </ul>
      </div>
    </div>
  </div>
</div>

{{-- Tópicos em Destaque --}}
<div class="container my-5">
    <h2 class="text-center mb-4 primaryTitles">Tópicos em Destaque</h2>

    @if($featuredTopics->isEmpty())
        <p class="text-center text-muted">Nenhum tópico disponível no momento.</p>
    @else
        <div class="row justify-content-center">
            @foreach($featuredTopics as $topic)
                <div class="col-md-6 col-lg-5 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="ratio ratio-16x9">
                            <img 
                                src="{{ asset($topic->image) }}" 
                                class="card-img-top rounded-top" 
                                alt="{{ $topic->title }}" 
                                style="object-fit: cover;"
                            >
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold">{{ $topic->title }}</h5>
                            <p class="card-text mb-3">
                                {{ Str::limit($topic->description, 140) }}
                            </p>
                            <div class="mt-auto">
                                <a href="{{ route('topics.show', $topic->id) }}" class="btn btn-outline-success w-100">
                                    Ler mais
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Seção informativa --}}
<div id="content" class="content mx-auto col-sm-12 col-md-12 col-lg-12 col-xl-10 row mb-3 border-bottom border-white">
    <h2 class="contentTitle text-center mt-4 mb-4">Entenda os benefícios da Fitoterapia.</h2>
    <div class="personalSite col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <h3 class="secondaryTitles">O que é:</h3>
        <div class="personalSiteText">
            <p>Fitoterapia é uma técnica que estuda as funções terapêuticas das plantas e vegetais para prevenção e tratamento de doenças. Médicos, nutricionistas, farmacêuticos, fisioterapeutas e outros profissionais são capacitados para indicar fitoterápicos aos seus pacientes, com o objetivo de melhorar o organismo, ajudar no combate de doenças e atuar na prevenção de problemas de saúde.</p>
        </div>
    </div>
    <div class="companySite col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <h3 class="secondaryTitles">Origem</h3>
        <div class="companySiteText">
            <p>O termo tem origem grega: “phyton”, que significa “vegetal”, e “therapeia”, que remete a “tratamento”. Desta forma, a técnica tem como base uma cultura milenar de uso das plantas para cuidar da saúde.</p>
            <p>Vale destacar que a fitoterapia é somada a estudos e análises no campo científico continuamente. Neste contexto, as pesquisas avaliam a atuação química, toxicológica e farmacológica das plantas medicinais e dos princípios ativos.</p>
        </div>
    </div>
</div>

@endsection

{{-- Script da busca --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let searchTimeout = null;

    $("#homeSearchInput").on("input", function() {
        const query = $(this).val().trim();
        clearTimeout(searchTimeout);

        if (query.length === 0) {
            $("#searchDropdown").addClass("d-none");
            return;
        }

        searchTimeout = setTimeout(() => {
            $.ajax({
                url: "/plants/search",
                method: "GET",
                data: { q: query },
                success: function(response) {
                    const $dropdown = $("#searchDropdown");
                    $dropdown.empty();

                    if (response.length > 0) {
                        response.forEach(plant => {

                            let tagsHtml = '';
                            if (plant.tags && plant.tags.length > 0) {
                                plant.tags.forEach(tag => {
                                    tagsHtml += `
                                        <span class="badge bg-success me-1">
                                            ${tag.name}
                                        </span>
                                    `;
                                });
                            }

                            $dropdown.append(`
                                <a href="/plant/${plant.id}/${plant.slug}"
                                   class="list-group-item list-group-item-action">

                                    <strong>${plant.popular_name}</strong><br>
                                    <small class="text-muted">
                                        ${plant.scientific_name}
                                    </small>

                                    <div class="mt-2">
                                        ${tagsHtml}
                                    </div>
                                </a>
                            `);
                        });
                    } else {
                        $dropdown.append(`
                            <div class="list-group-item text-center text-muted">
                                Nenhum resultado encontrado.
                            </div>
                        `);
                    }

                    $dropdown.removeClass("d-none");
                },
                error: function() {
                    $("#searchDropdown")
                        .html(`
                            <div class="list-group-item text-center text-danger">
                                Erro na busca.
                            </div>
                        `)
                        .removeClass("d-none");
                }
            });
        }, 300);
    });

    // Fecha dropdown ao clicar fora
    $(document).on("click", function(e) {
        if (!$(e.target).closest("#homeSearchInput, #searchDropdown").length) {
            $("#searchDropdown").addClass("d-none");
        }
    });
});
</script>



