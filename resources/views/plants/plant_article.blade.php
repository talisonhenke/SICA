@extends('layouts.main')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @foreach ($plants as $plant)
                <div class="bg-white article-header mt-3 mb-2">
                    <p class="article-title text-black mx-2" style="text-transform:uppercase">{{ $plant->popular_name }}</p>
                    <strong class="mx-2">Publicado em:</strong> {{ $plant->created_at}}
                </div>
                <div class="bg-white article-body mb-2">
                    <strong class="mx-2">Nome Científico:</strong> {{ $plant->scientific_name }} <br>
                    <strong class="mx-2">Nome Popular:</strong> {{ $plant->popular_name }} <br>
                    <div class="article-img mx-auto col-sm-10 col-lg-6">
                        <img src="/images/plants/{{ $plant->images }}" class="img-fluid article-image">
                    </div>
                    <div class="article-content mx-auto col-sm-10 col-lg-10">
                        <p class="article-text m-0">{{ $plant->popular_use }}</p>
                    </div>
                </div>
                <div class="extra-info bg-white mb-2">
                    <div class="extra-info-style mx-2">
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