@extends('layouts.main')

@section('content')

<h2 class="mb-4">Moderação de Comentários</h2>

{{-- Filtros --}}
<div class="mb-3 d-flex gap-2">
    <a href="{{ route('admin.moderation.index', ['filter' => 'all']) }}"
       class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
        Todos
    </a>

    <a href="{{ route('admin.moderation.index', ['filter' => 'reported']) }}"
       class="btn btn-sm {{ $filter === 'reported' ? 'btn-primary' : 'btn-outline-primary' }}">
        Reportados
    </a>

    <a href="{{ route('admin.moderation.index', ['filter' => 'suspect']) }}"
       class="btn btn-sm {{ $filter === 'suspect' ? 'btn-warning' : 'btn-outline-warning' }}">
        Tóxicos ≥ 0.5
    </a>

    <a href="{{ route('admin.moderation.index', ['filter' => 'high']) }}"
       class="btn btn-sm {{ $filter === 'high' ? 'btn-danger' : 'btn-outline-danger' }}">
        Tóxicos ≥ 0.7
    </a>
</div>

{{-- Tabela --}}
<div class="card p-3">
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

                    <td>
                        {{ number_format($comment->toxicity_level, 2) }}
                    </td>

                    <td>
                        @if($comment->reported)
                            <span class="badge bg-danger">Sim</span>
                        @else
                            <span class="badge bg-success">Não</span>
                        @endif
                    </td>

                    <td>
                        <button class="btn btn-sm btn-info"
                                data-bs-toggle="modal"
                                data-bs-target="#moderateModal{{ $comment->id }}">
                            Moderar
                        </button>
                    </td>
                </tr>

                {{-- Modal --}}
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

                            <div class="modal-footer d-flex justify-content-between">

                                {{-- EXCLUIR + STRIKE --}}
                                <form action="{{ route('topic-comments.moderateDelete', $comment->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Excluir e dar STRIKE ao usuário?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger">Excluir + Strike</button>
                                </form>

                                {{-- PERMITIR COMENTÁRIO --}}
                                <form action="{{ route('topic-comments.allow', $comment->id) }}"
                                      method="POST">
                                    @csrf
                                    <button class="btn btn-success">
                                        Permitir Comentário
                                    </button>
                                </form>

                                {{-- BLOQUEAR TODOS OS COMENTÁRIOS DO USUÁRIO --}}
                                <form action="{{ route('topic-comments.blockUser', $comment->user->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Bloquear comentários deste usuário?');">
                                    @csrf
                                    <button class="btn btn-dark">
                                        Bloquear Usuário
                                    </button>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>

            @endforeach
        </tbody>
    </table>

    {{ $comments->links() }}

</div>

@endsection
