@extends('layouts.main')

@section('content')
    <style>
        /* Card mobile mais estreito para evitar overflow */
        .mobile-comment-card {
            max-width: 100%;
            min-width: 260px;
            margin: 0 auto;
        }

        /* Select esteticamente agradável */
        .filter-select {
            max-width: 250px;
        }

        /* Impede qualquer overflow horizontal no mobile */
        body,
        html {
            overflow-x: hidden !important;
        }

        /* Remove padding lateral do container em telas pequenas */
        @media (max-width: 576px) {
            .container {
                padding-left: 0 !important;
                padding-right: 0 !important;
                overflow-x: hidden !important;
            }

            .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            .col-12,
            .col-lg-10 {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            /* O card estava excedendo um pouco pelas bordas */
            .card {
                border-radius: 0;
                width: 100% !important;
                max-width: 100% !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
                box-sizing: border-box;
            }

            /* Comentários mobile */
            .mobile-comment-card {
                width: 100% !important;
                margin: 0 !important;
                box-sizing: border-box;
            }

            .filter-select {
                width: 100%;
            }

            .modal-footer {
                flex-direction: column !important;
                align-items: center !important;
                gap: 10px;
                /* Espaço vertical entre os botões */
            }

            .modal-footer form {
                width: 100%;
            }

            .modal-footer button {
                width: 100%;
                /* Botões largura total no mobile */
            }
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">

                <h2 class="mb-4 primaryTitles">Moderação de Comentários</h2>

                {{-- SELECT DE FILTRO --}}
                <div class="mb-3">
                    <select id="filterSelect" class="form-select filter-select">
                        <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>Todos</option>
                        <option value="reported" {{ $filter === 'reported' ? 'selected' : '' }}>Reportados</option>
                        <option value="suspect" {{ $filter === 'suspect' ? 'selected' : '' }}>Suspeitos ≥ 0.5</option>
                        <option value="high" {{ $filter === 'high' ? 'selected' : '' }}>Graves ≥ 0.7</option>
                    </select>
                </div>

                <script>
                    document.getElementById('filterSelect').addEventListener('change', function() {
                        const chosen = this.value;
                        window.location.href = "{{ route('admin.moderation.index') }}?filter=" + chosen;
                    });
                </script>

                {{-- LISTA / TABELA --}}
                <div class="card p-3">

                    {{-- DESKTOP TABLE --}}
                    <div class="d-none d-md-block">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Usuário</th>
                                    <th>Comentário</th>
                                    <th>Toxicidade</th>
                                    <th>Reportado</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($comments as $comment)
                                    <tr>
                                        <td>{{ $comment->id }}</td>
                                        <td>{{ $comment->user->name }}</td>
                                        <td>{{ Str::limit($comment->comment, 70) }}</td>
                                        <td>{{ number_format($comment->toxicity_level, 2) }}</td>
                                        <td>
                                            @if ($comment->reported)
                                                <span class="badge bg-danger">Sim</span>
                                            @else
                                                <span class="badge bg-success">Não</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                data-bs-target="#moderateModal{{ $comment->id }}">
                                                Moderar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- MOBILE CARDS --}}
                    <div class="d-md-none">
                        @foreach ($comments as $comment)
                            <div class="mobile-comment-card border rounded p-3 mb-3 bg-light">

                                <div><strong>ID:</strong> {{ $comment->id }}</div>

                                <div><strong>Usuário:</strong> {{ $comment->user->name }}</div>

                                <div><strong>Toxicidade:</strong>
                                    {{ number_format($comment->toxicity_level, 2) }}
                                </div>

                                <div>
                                    <strong>Reportado:</strong>
                                    @if ($comment->reported)
                                        <span class="badge bg-danger">Sim</span>
                                    @else
                                        <span class="badge bg-success">Não</span>
                                    @endif
                                </div>

                                <div class="mt-2">
                                    <strong>Comentário:</strong><br>
                                    {{ Str::limit($comment->comment, 200) }}
                                </div>

                                <button class="btn btn-sm btn-info mt-3 w-100" data-bs-toggle="modal"
                                    data-bs-target="#moderateModal{{ $comment->id }}">
                                    Moderar
                                </button>
                            </div>
                        @endforeach
                    </div>

                    {{ $comments->links() }}

                </div>

            </div>
        </div>
    </div>

    {{-- MODAIS --}}
    @foreach ($comments as $comment)
        @php
            $baseRoute = $comment->comment_type === 'topic' ? 'topic-comments' : 'plant-comments';
        @endphp

        <div class="modal fade" id="moderateModal{{ $comment->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Moderar Comentário #{{ $comment->id }}
                        </h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <strong>Usuário:</strong> {{ $comment->user->name }} <br>
                        <strong>Toxicidade:</strong> {{ $comment->toxicity_level }}<br>
                        <strong>Reportado:</strong> {{ $comment->reported ? 'Sim' : 'Não' }}<br>
                        <hr>
                        <p>{{ $comment->comment }}</p>
                    </div>

                    <div class="modal-footer d-flex justify-content-around">
                        <form action="{{ route($baseRoute . '.moderateDelete', $comment->id) }}" method="POST"
                            onsubmit="return confirm('Excluir e dar STRIKE ao usuário?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Excluir + Strike</button>
                        </form>


                        <form action="{{ route($baseRoute . '.allow', $comment->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-success">Permitir Comentário</button>
                        </form>


                        <form action="{{ route('topic-comments.blockUser', $comment->user->id) }}" method="POST"
                            onsubmit="return confirm('Bloquear comentários deste usuário?');">
                            @csrf
                            <button class="btn btn-dark">Bloquear Usuário</button>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endsection
