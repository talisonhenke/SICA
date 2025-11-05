@extends('layouts.main')
@section('content')
<style>
    .article-container {
        max-width: 950px;
        margin: 2rem auto;
        background-color: var(--color-surface);
        border-radius: 1rem;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 2rem;
    }

    .admin-options {
        background-color: var(--color-bg);
        border-radius: 0.75rem;
        padding: 1rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .admin-options h3 {
        color: var(--color-text-dark);
        margin-bottom: 0.8rem;
    }

    .admin-options .btn {
        margin: 0 0.25rem;
    }

    .article-header {
        border-bottom: 2px solid var(--color-accent);
        margin-bottom: 1.2rem;
        padding-bottom: 0.8rem;
    }

    .article-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--color-primary);
        text-transform: uppercase;
        margin-bottom: 0.2rem;
    }

    .article-body strong {
        color: var(--color-secondary);
    }

    .article-content p {
        line-height: 1.8;
        font-size: 1.05rem;
        color: var(--color-text);
        text-align: justify;
        margin-bottom: 1rem;
    }

    .article-content p.indent:first-child {
        text-indent: 2rem;
    }

    /* Carrossel */
    .plant-carousel-img {
        height: 420px;
        object-fit: cover;
        border-radius: 0.75rem;
    }

    .custom-carousel-btn {
        width: 55px;
        height: 55px;
        top: 50%;
        transform: translateY(-50%);
        background-color: var(--color-surface);
        border-radius: 50%;
        transition: background-color 0.3s;
        margin: 0 10px;
        z-index: 3;
        opacity: 1;
    }

    .custom-carousel-btn:hover {
        background-color: var(--color-success);
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        filter: invert(1);
        width: 25px;
        height: 25px;
    }

    .carousel-gradient-left,
    .carousel-gradient-right {
        position: absolute;
        top: 0;
        width: 70px;
        height: 100%;
        z-index: 2; /* abaixo dos botões (que estão em z-index: auto/3) */
        pointer-events: none;
    }

    .carousel-gradient-left {
        left: 0;
        background: linear-gradient(to right, rgba(0, 0, 0, 0.6), transparent);
    }

    .carousel-gradient-right {
        right: 0;
        background: linear-gradient(to left, rgba(0, 0, 0, 0.6), transparent);
    }


    .thumbnail-container {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .thumbnail-img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 0.5rem;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .thumbnail-img:hover,
    .thumbnail-img.active {
        border-color: var(--color-accent);
        transform: scale(1.05);
    }

    /* Seção de informações adicionais */
    .extra-info {
        margin-top: 2rem;
        background-color: var(--color-bg);
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .extra-info h2 {
        text-align: center;
        color: var(--color-primary);
        margin-bottom: 1rem;
    }

    .extra-info strong {
        color: var(--color-secondary);
    }

    .items-list {
        list-style-type: disc;
        margin-left: 1.5rem;
        color: var(--color-text);
    }

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

<div class="article-container">
    {{-- Opções do administrador --}}
    @if(Auth::check() && Auth::user()->user_lvl === 'admin')
        <div class="admin-options">
            <h3>⚙️ Opções do Administrador</h3>
            <a href="{{ route('plants.edit', $plant->id) }}" class="btn btn-warning btn-sm">Editar</a>
            <form action="{{ route('plants.destroy', $plant->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta planta?')">
                    Excluir
                </button>
            </form>
        </div>
    @endif

    {{-- Cabeçalho do artigo --}}
    <div class="article-header">
        <h1 class="article-title">{{ $plant->popular_name }}</h1>
        <small><strong>Publicado em:</strong> {{ $plant->created_at->format('d/m/Y') }}</small>
    </div>

    <div class="article-body">
        <p><strong>Nome Científico:</strong> {{ $plant->scientific_name }}</p>
        <p><strong>Nome Popular:</strong> {{ $plant->popular_name }}</p>

        {{-- Carrossel de imagens --}}
        @php
            $images = is_array($plant->images) ? $plant->images : json_decode($plant->images, true);
        @endphp

        @if (!empty($images) && count($images) > 0)
        <div class="mx-auto col-sm-10 col-lg-8 mb-4">
            <div id="plantCarousel{{ $plant->id }}" class="carousel slide" data-bs-ride="false" data-bs-interval="false">
    <div class="carousel-inner">
        @foreach ($images as $index => $image)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ asset($image) }}" class="d-block w-100 plant-carousel-img" alt="Imagem da planta">
            </div>
        @endforeach
    </div>

    <!-- Gradientes laterais -->
    <div class="carousel-gradient-left"></div>
    <div class="carousel-gradient-right"></div>

    <button class="carousel-control-prev custom-carousel-btn" type="button" data-bs-target="#plantCarousel{{ $plant->id }}" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next custom-carousel-btn" type="button" data-bs-target="#plantCarousel{{ $plant->id }}" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>


            {{-- Miniaturas --}}
            <div class="thumbnail-container">
                @foreach ($images as $index => $image)
                    <img src="{{ asset($image) }}" class="thumbnail-img {{ $index === 0 ? 'active' : '' }}" 
                        data-bs-target="#plantCarousel{{ $plant->id }}" data-bs-slide-to="{{ $index }}">
                @endforeach
            </div>
        </div>
        @endif

        {{-- Corpo do artigo --}}
        <div class="article-content">
            @php
                $paragraphs = preg_split('/\r\n|\r|\n/', $plant->popular_use);
                $paragraphs = array_filter($paragraphs, fn($p) => trim($p) !== '');
            @endphp

            @foreach ($paragraphs as $p)
                <p class="indent">{{ $p }}</p>
            @endforeach
        </div>
    </div>

    {{-- Informações adicionais --}}
    <div class="extra-info">
        <h2>Informações Adicionais</h2>
        <p><strong>Habitat:</strong> {{ $plant->habitat }}</p>
        <p><strong>Partes Utilizadas:</strong></p>
        <ul class="items-list">
            @foreach ($plant->useful_parts as $part)
                <li>{{ $part }}</li>
            @endforeach
        </ul>
        <p><strong>Características:</strong> {{ $plant->characteristics }}</p>
        <p><strong>Observações:</strong> {{ $plant->observations }}</p>
        <p><strong>Composição Química:</strong> {{ $plant->chemical_composition }}</p>
        <p><strong>Contraindicações:</strong> {{ $plant->contraindications }}</p>
        <p><strong>Modo de Uso:</strong> {{ $plant->mode_of_use }}</p>
        <p><strong>Referências:</strong> {{ $plant->info_references }}</p>
        <p><strong>Tags:</strong> {{ $plant->tags }}</p>
    </div>
</div>

{{-- Script para miniaturas --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const carouselId = '#plantCarousel{{ $plant->id }}';
    const thumbnails = document.querySelectorAll('[data-bs-target="' + carouselId + '"]');
    const carousel = document.querySelector(carouselId);

    carousel.addEventListener('slide.bs.carousel', function (event) {
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        thumbnails[event.to].classList.add('active');
    });
});
</script>
@endsection
