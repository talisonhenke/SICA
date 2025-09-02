@extends('layouts.main')
@section('content')
<div class="container mx-0 px-0">
    <div class="row justify-content-center p-0 m-0">
        <div class="col-md-10 col-sm-12 overflow-hidden p-0 m-0">
            @foreach ($plants as $plant)
                <div class="bg-white article-header mt-3 mb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="article-title text-black mx-2" style="text-transform:uppercase">{{ $plant->popular_name }}</p>
                        <strong class="mx-2">Publicado em:</strong> {{ $plant->created_at}}
                    </div>

                    {{-- Verifica se o usuário está logado e se é admin --}}
                    @if(Auth::check() && Auth::user()->user_lvl === 'admin')
                        <div>
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
                </div>


                <div class="bg-white article-body mb-2 px-2">
                    <strong class="mx-0">Nome Científico:</strong> {{ $plant->scientific_name }} <br>
                    <strong class="mx-0">Nome Popular:</strong> {{ $plant->popular_name }} <br>
                    <div class="article-img mx-auto col-sm-10 col-lg-6">
                        <img src="/images/plants/{{ $plant->images }}" class="img-fluid article-image">
                    </div>
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
