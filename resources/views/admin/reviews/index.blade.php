@extends('layouts.main')

@section('content')

<div class="container py-4">

    <h3 class="mb-3 primaryTitles">Avaliações do Site</h3>

    {{-- MÉDIA GERAL --}}
    <div class="card p-3 mb-4">
        <h5 class="mb-2">Avaliação Geral</h5>

        <div class="d-flex align-items-center" style="font-size: 1.6rem">

            {{-- Exibir estrelas --}}
            @php
                $full = floor($rounded);
                $half = ($rounded - $full) === 0.5;
            @endphp

            @for ($i = 1; $i <= $full; $i++)
                <i class="bi bi-star-fill text-warning"></i>
            @endfor

            @if ($half)
                <i class="bi bi-star-half text-warning"></i>
            @endif

            {{-- Estrelas vazias restantes --}}
            @for ($i = $full + ($half ? 1 : 0); $i < 5; $i++)
                <i class="bi bi-star text-warning"></i>
            @endfor

            <span class="ms-2 text-muted">
                Média: {{ number_format($average, 2) }}
            </span>

        </div>
    </div>

    {{-- LISTAGEM --}}
    <div class="card p-3">

        {{-- DESKTOP TABLE --}}
        <div class="d-none d-md-block">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Usuário</th>
                        <th>Avaliação</th>
                        <th>Comentário</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($reviews as $review)
                        <tr>
                            <td>{{ $review->id }}</td>
                            <td>{{ $review->user->name }}</td>

                            <td>
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : ' text-secondary' }}"></i>
                                @endfor
                            </td>

                            <td>{{ Str::limit($review->comment, 100) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- MOBILE VIEW --}}
        <div class="d-md-none">
            @foreach ($reviews as $review)
                <div class="mobile-comment-card border rounded p-3 mb-3 bg-light">

                    <div><strong>ID:</strong> {{ $review->id }}</div>

                    <div><strong>Usuário:</strong> {{ $review->user->name }}</div>

                    <div class="mt-2">
                        <strong>Avaliação:</strong><br>
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : ' text-secondary' }}"></i>
                        @endfor
                    </div>

                    <div class="mt-2">
                        <strong>Comentário:</strong><br>
                        {{ $review->comment ?: '—' }}
                    </div>

                </div>
            @endforeach
        </div>

        {{ $reviews->links() }}

    </div>

</div>

@endsection
