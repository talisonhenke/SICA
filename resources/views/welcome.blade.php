@extends('layouts.main')
@section('content')

{{-- Seção principal com busca --}}
<div class="container text-center mt-5 mb-5">
    <h2 class="mb-4">Busque por plantas medicinais</h2>
    <div class="input-group mb-3 mx-auto col-sm-12 col-md-8 col-lg-6">
        <input type="text" id="homeSearchInput" class="form-control form-control-lg" placeholder="Digite o nome da planta...">
        <button class="btn btn-success btn-lg" type="button" id="searchButton">
            <i class="bi bi-search"></i> Buscar
        </button>
    </div>
</div>

{{-- Modal para exibir resultados --}}
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content bg-light">
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
    <h2 class="text-center mb-4">Tópicos em Destaque</h2>

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
                            <p class="card-text text-muted mb-3">
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

{{-- Seções originais (mantidas por enquanto) --}}
<div id="content" class="content mx-auto col-sm-12 col-md-12 col-lg-12 col-xl-10 row mb-3 border-bottom border-white">
    <h2 class="contentTitle text-center mt-4 mb-4">Entenda os benefícios da Fitoterapia.</h2>
    <div class="personalSite col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <h3 class="personalSitetitle">O que é:</h3>
        <div class="personalSiteText">
            <p>Fitoterapia é uma técnica que estuda as funções terapêuticas das plantas e vegetais para prevenção e tratamento de doenças. Médicos, nutricionistas, farmacêuticos, fisioterapeutas e outros profissionais são capacitados para indicar fitoterápicos aos seus pacientes, com o objetivo de melhorar o organismo, ajudar no combate de doenças e atuar na prevenção de problemas de saúde.</p>
        </div>
    </div>
    <div class="companySite col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <h3 class="companySitetitle">Origem</h3>
        <div class="companySiteText">
            <p>O termo tem origem grega: “phyton”, que significa “vegetal”, e “therapeia”, que remete a “tratamento”. Desta forma, a técnica tem como base uma cultura milenar de uso das plantas para cuidar da saúde.</p>
            <p>Vale destacar que a fitoterapia é somada a estudos e análises no campo científico continuamente. Neste contexto, as pesquisas avaliam a atuação química, toxicológica e farmacológica das plantas medicinais e dos princípios ativos.</p>
        </div>
    </div>
</div>


@endsection

{{-- Script da busca --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let searchTimeout = null;

    // Ao digitar no campo de busca
    $("#homeSearchInput").on("input", function() {
        const query = $(this).val().trim();

        // Cancela qualquer requisição anterior (para evitar flood)
        clearTimeout(searchTimeout);

        // Se o campo estiver vazio, fecha o modal
        if (query.length === 0) {
            $("#searchModal").modal("hide");
            return;
        }

        // Espera 300ms após parar de digitar para buscar (debounce)
        searchTimeout = setTimeout(() => {
            $.ajax({
                url: "/plants/search",
                method: "GET",
                data: { q: query },
                success: function(response) {
                    $("#searchResults").empty();

                    if (response.length > 0) {
                        response.forEach(plant => {
                            $("#searchResults").append(`
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="/plant/${plant.id}/${plant.popular_name}" 
                                       class="text-decoration-none text-dark w-100">
                                        ${plant.popular_name} <br><small class="text-muted">${plant.scientific_name}</small>
                                    </a>
                                </li>
                            `);
                        });
                    } else {
                        $("#searchResults").append(`<li class="list-group-item text-center text-muted">Nenhum resultado encontrado.</li>`);
                    }

                    // Exibe o modal automaticamente se houver texto digitado
                    if (!$('#searchModal').hasClass('show')) {
                        $("#searchModal").modal("show");
                    }
                },
                error: function() {
                    $("#searchResults").html('<li class="list-group-item text-center text-danger">Erro na busca. Tente novamente.</li>');
                    if (!$('#searchModal').hasClass('show')) {
                        $("#searchModal").modal("show");
                    }
                }
            });
        }, 300); // tempo de debounce
    });
});
</script>

