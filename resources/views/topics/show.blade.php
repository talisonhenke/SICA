@extends('layouts.main')

@section('content')
    <style>
        .topic-container {
            max-width: 850px;
            margin: 3rem auto;
            background-color: var(--color-surface-primary);
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .topic-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--color-secondary);
            text-align: center;
            margin-bottom: 0.8rem;
        }

        .topic-description {
            font-size: 1.1rem;
            color: var(--color-text-secondary);
            text-align: center;
            margin-bottom: 1.8rem;
        }

        .topic-image {
            display: block;
            width: 100%;
            max-width: 600px;
            height: auto;
            border-radius: 0.75rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        }

        .topic-content {
            text-align: justify;
            line-height: 1.8;
            color: var(--color-text);
            font-size: 1.05rem;
            white-space: pre-line;
        }

        .plant-ref {
            color: var(--color-accent);
            font-weight: 600;
            text-decoration: none;
            /* border-bottom: 1px dashed var(--color-accent); */
            transition: color 0.2s ease, border-color 0.2s ease;
        }

        .plant-ref:hover {
            color: var(--color-secondary);
            /* border-color: var(--color-secondary); */
            text-decoration: none;
        }

        .button-group {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.8rem;
            margin-top: 2rem;
        }

        .btn-action {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 0.6rem;
            font-weight: 600;
            text-decoration: none;
            color: #fff;
            transition: background 0.3s ease, transform 0.1s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .btn-edit {
            background-color: var(--color-accent);
        }

        .btn-edit:hover {
            background-color: var(--color-secondary);
        }

        .btn-delete {
            background-color: var(--color-danger);
        }

        .btn-delete:hover {
            background-color: #b71c1c;
        }

        .btn-back {
            background-color: var(--color-primary);
        }

        .btn-back:hover {
            background-color: var(--color-primary-light);
        }

        form {
            display: inline;
        }

        .comments-section {
            margin-top: 3rem;
        }

        .avatar-circle {
            width: 48px;
            height: 48px;
            background: var(--color-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 1.2rem;
            user-select: none;
        }

        .comment-box {
            background: var(--color-surface-primary);
        }
    </style>

    <div class="topic-container">
        <div class="button-group mb-4">
            {{-- Voltar --}}
            <a href="{{ route('topics.index') }}" class="btn-action btn-back">← Voltar</a>

            {{-- Botões de admin --}}
            @if (Auth::check() && Auth::user()->user_lvl === 'admin')
                <a href="{{ route('topics.edit', $topic->id) }}" class="btn-action btn-edit">✏️ Editar</a>

                <form action="{{ route('topics.destroy', $topic->id) }}" method="POST"
                    onsubmit="return confirm('Tem certeza que deseja excluir este tópico?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-delete">🗑️ Excluir</button>
                </form>
            @endif
        </div>
        <h1 class="topic-title">{{ $topic->title }}</h1>
        <p class="topic-description">{{ $topic->description }}</p>

        @if ($topic->image)
            <img src="{{ asset($topic->image) }}" alt="{{ $topic->title }}" class="topic-image">
        @endif

        <div class="topic-content">
            {{-- {!! nl2br(e($topic->content)) !!} --}}
            {!! $topic->content_html !!}
        </div>

        <hr class="my-5">

        <div class="comments-section">
            <h3 class="mb-3">Comentários</h3>

            {{-- Formulário para comentar --}}
            <div class="card mb-4 p-3">

                @guest
                    <textarea class="form-control mb-2" rows="3" placeholder="Faça login para comentar..." disabled></textarea>
                    <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#loginModal">
                        Entrar para comentar
                    </button>
                @else
                    {{-- Verificação de STRIKES --}}
                    @if (auth()->user()->comment_strikes >= 3)
                        <div class="alert alert-warning mb-0">
                            A função de comentários está temporariamente indisponível para você.
                        </div>
                    @else
                        <form action="{{ route('topics.comments.store', $topic->id) }}" method="POST">
                            @csrf
                            <textarea name="comment" class="form-control mb-2" rows="3" required placeholder="Escreva seu comentário..."></textarea>
                            <button class="btn btn-primary">Enviar Comentário</button>
                        </form>
                    @endif
                @endguest

            </div>


            {{-- Lista de comentários --}}
            @foreach ($topic->comments as $comment)
                <div class="comment-box d-flex p-3 mb-3 rounded shadow-sm">

                    {{-- Avatar --}}
                    <div class="comment-avatar me-3">
                        <div class="avatar-circle">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </div>
                    </div>

                    <div class="comment-content flex-grow-1">

                        <strong>{{ $comment->user->name }}</strong>
                        <small class="text-muted d-block">
                            {{ $comment->created_at->format('d/m/Y H:i') }}
                        </small>

                        <p class="mt-2">{{ $comment->comment }}</p>

                        {{-- Botões --}}
                        <div class="d-flex gap-2">

                            {{-- Editar — somente dono --}}
                            @if (Auth::check() && Auth::id() === $comment->user_id)
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editCommentModal{{ $comment->id }}">
                                    Editar
                                </button>
                            @endif

                            {{-- Excluir — somente dono --}}
                            @if (Auth::check() && Auth::id() === $comment->user_id)
                                <form action="{{ route('topic-comments.destroy', $comment->id) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza que deseja excluir este comentário?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        Excluir
                                    </button>
                                </form>
                            @endif

                            {{-- Moderação — SOMENTE ADMIN --}}
                            @if (Auth::check() && Auth::user()->user_lvl === 'admin')
                                <form action="{{ route('topic-comments.moderateDelete', $comment->id) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza que deseja EXCLUIR este comentário para fins de moderação?\n\nEssa ação dará um STRIKE ao usuário autor do comentário. Ao atingir 3 strikes, ele será impedido de comentar até ser desbloqueado.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-warning">
                                        Moderar (Excluir + Strike)
                                    </button>
                                </form>
                            @endif

                            {{-- NOVO: Denunciar — Todos logados, exceto o dono do comentário --}}
                            @if (Auth::check() && Auth::id() !== $comment->user_id)
                                <form action="{{ route('topic-comments.report', $comment->id) }}" method="POST"
                                    onsubmit="return confirm('Deseja denunciar este comentário?');">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning">
                                        <i class="fa fa-flag"></i> Denunciar
                                    </button>
                                </form>
                            @endif

                        </div>

                    </div>
                </div>

                {{-- Modal de edição --}}
                @if (Auth::check() && Auth::id() === $comment->user_id)
                    <div class="modal fade" id="editCommentModal{{ $comment->id }}">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Comentário</h5>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <form action="{{ route('topic-comments.update', $comment->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-body">
                                        <textarea name="comment" rows="4" class="form-control">{{ $comment->comment }}</textarea>
                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-primary">Salvar</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>


    </div>
@endsection
