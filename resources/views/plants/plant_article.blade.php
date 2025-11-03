@extends('layouts.main')
@section('content')
<div class="container mx-auto px-0">
    <div class="row justify-content-center p-0 m-0">
        <div class="col-md-10 col-sm-12 overflow-hidden p-0 m-0">
            @foreach ($plants as $plant)
                {{-- Verifica se o usuário está logado e se é admin --}}
                    @if(Auth::check() && Auth::user()->user_lvl === 'admin')
                        <div class="bg-light text-center py-2 mt-2">
                            <h3 style="color: #000">Opções do administrador</h3>
                            {{-- Botão Editar --}}
                            <a href="{{ route('plants.edit', $plant->id) }}" class="btn btn-sm btn-warning">Editar</a>

                            {{-- Botão Excluir --}}
                            <form action="{{ route('plants.destroy', $plant->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger ms-2 me-4" onclick="return confirm('Tem certeza que deseja excluir esta planta?')">Excluir</button>
                            </form>
                        </div>
                    @endif
                <div class="bg-white article-header mt-3 mb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="article-title text-black mx-2" style="text-transform:uppercase">{{ $plant->popular_name }}</p>
                        <strong class="mx-2">Publicado em:</strong> {{ $plant->created_at}}
                    </div>
                </div>


                <div class="bg-white article-body mb-2 px-2">
                    <strong class="mx-0">Nome Científico:</strong> {{ $plant->scientific_name }} <br>
                    <strong class="mx-0">Nome Popular:</strong> {{ $plant->popular_name }} <br>
                    
                    @php
    // Garante que o campo images seja um array válido
    $images = is_array($plant->images) ? $plant->images : json_decode($plant->images, true);
@endphp

@if (!empty($images) && count($images) > 0)
    <div class="mx-auto col-sm-10 col-lg-6">
        <!-- Carrossel principal -->
        <div id="plantCarousel{{ $plant->id }}" class="carousel slide" data-bs-ride="false" data-bs-interval="false">
            <div class="carousel-inner rounded shadow">
                @foreach ($images as $index => $image)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ asset($image) }}" class="d-block w-100 plant-carousel-img" alt="Imagem da planta">
                    </div>
                @endforeach
            </div>

            <!-- Botões de navegação -->
            <button class="carousel-control-prev custom-carousel-btn" type="button" data-bs-target="#plantCarousel{{ $plant->id }}" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next custom-carousel-btn" type="button" data-bs-target="#plantCarousel{{ $plant->id }}" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <!-- Miniaturas -->
        <div class="thumbnail-container mt-3 d-flex justify-content-center flex-wrap gap-2">
            @foreach ($images as $index => $image)
                <img 
                    src="{{ asset($image) }}" 
                    class="thumbnail-img {{ $index === 0 ? 'active' : '' }}" 
                    data-bs-target="#plantCarousel{{ $plant->id }}" 
                    data-bs-slide-to="{{ $index }}" 
                    alt="Miniatura da planta"
                >
            @endforeach
        </div>
    </div>
@endif

<!-- Estilos customizados -->
<style>
    .plant-carousel-img {
        height: 400px;
        object-fit: cover;
        object-position: center;
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    /* Botões de navegação */
    .custom-carousel-btn {
        width: 50px;
        height: 50px;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        transition: background-color 0.3s ease;
    }

    .custom-carousel-btn:hover {
        background-color: rgba(0, 0, 0, 0.7);
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        filter: invert(1);
        width: 20px;
        height: 20px;
    }

    /* Miniaturas */
    .thumbnail-container {
        overflow-x: auto;
        scrollbar-width: thin;
    }

    .thumbnail-img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border: 2px solid transparent;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.2s, border-color 0.2s;
    }

    .thumbnail-img:hover {
        transform: scale(1.05);
        border-color: #198754; /* verde Bootstrap */
    }

    .thumbnail-img.active {
        border-color: #198754;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .plant-carousel-img {
            height: 250px;
        }
        .custom-carousel-btn {
            width: 40px;
            height: 40px;
        }
        .thumbnail-img {
            width: 55px;
            height: 55px;
        }
    }
</style>

<!-- Script para ativar miniaturas -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const carouselId = '#plantCarousel{{ $plant->id }}';
        const thumbnails = document.querySelectorAll('[data-bs-target="' + carouselId + '"]');
        const carousel = document.querySelector(carouselId);

        // Atualiza o destaque da miniatura conforme o slide muda
        carousel.addEventListener('slide.bs.carousel', function (event) {
            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            thumbnails[event.to].classList.add('active');
        });
    });
</script>


                    <div class="article-content mx-auto col-sm-10 col-lg-10">
                        {{-- <p class="article-text mx-0 mt-4">{!! nl2br(e($plant->popular_use)) !!}</p> --}}
                        @php
                            // Quebra o texto em parágrafos
                            $paragraphs = preg_split('/\r\n|\r|\n/', $plant->popular_use);

                            // Remove linhas em branco (caso existam)
                            $paragraphs = array_filter($paragraphs, fn($p) => trim($p) !== '');
                        @endphp

                        <div class="article-text mx-0 mt-4">
                            @foreach ($paragraphs as $p)
                                <p class="indent">{{ $p }}</p>
                            @endforeach
                        </div>
                        <br>
                    </div>
                </div>

                <div class="extra-info bg-white mb-2">
                    <div class="extra-info-style mx-2 py-2">
                        <div class="text-center"><h2 class="text-primary">Informações adicionais</h2></div>
                        <div><strong>Habitat:</strong> {{ $plant->habitat }}</div>
                        <div><strong>Partes Utilizadas:</strong>
                            <ul class="items-list">
                                @foreach ($plant->useful_parts as $part)
                                    <li><span class="uselful_parts_text">{{ $part }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                        <div><strong>Características:</strong> {{ $plant->characteristics }}</div>
                        <div><strong>Observações:</strong> {{ $plant->observations }}</div>
                        <div><strong>Composição Química:</strong> {{ $plant->chemical_composition }}</div>
                        <div><strong>Contraindicações:</strong> {{ $plant->contraindications }}</div>
                        <div><strong>Modo de Uso:</strong> {{ $plant->mode_of_use }}</div>
                        <div><strong>Referências:</strong> {{ $plant->info_references }}</div>
                        <div><strong>Tags:</strong> {{ $plant->tags }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
