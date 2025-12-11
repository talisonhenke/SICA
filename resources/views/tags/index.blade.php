@extends('layouts.main')

@include('includes.toast')

@section('content')

<style>
    :root {
        --primary-color: #4CAF50;
        --primary-dark: #2E7D32;
    }
</style>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Tags</h2>

        <!-- Botão de adicionar -->
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTagModal">
            + Nova Tag
        </button>
    </div>

    <!-- CARD PRINCIPAL -->
    <div class="card p-3">

        {{-- DESKTOP TABLE --}}
        <div class="d-none d-md-block">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th style="width: 160px;">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($tags as $tag)
                        <tr>
                            <td>{{ $tag->id }}</td>
                            <td>{{ $tag->name }}</td>
                            <td>{{ Str::limit($tag->description, 90) }}</td>

                            <td class="text-center">

                                <!-- EDITAR -->
                                <button class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editTagModal{{ $tag->id }}">
                                    Editar
                                </button>

                                <!-- EXCLUIR -->
                                <button class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteTagModal{{ $tag->id }}">
                                    Excluir
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- MOBILE CARDS --}}
        <div class="d-md-none">
            @foreach ($tags as $tag)
                <div class="border rounded p-3 mb-3 bg-light">

                    <div><strong>ID:</strong> {{ $tag->id }}</div>
                    <div><strong>Nome:</strong> {{ $tag->name }}</div>
                    <div><strong>Descrição:</strong> {{ Str::limit($tag->description, 200) }}</div>

                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-primary btn-sm w-50"
                            data-bs-toggle="modal" data-bs-target="#editTagModal{{ $tag->id }}">
                            Editar
                        </button>

                        <button class="btn btn-danger btn-sm w-50"
                            data-bs-toggle="modal" data-bs-target="#deleteTagModal{{ $tag->id }}">
                            Excluir
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>


{{-- ============= MODAL: CRIAR TAG ============= --}}
<div class="modal fade" id="createTagModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('tags.store') }}" class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Nova Tag</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Nome da Tag</label>
                    <input type="text" name="name" class="form-control" required maxlength="255">
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição (opcional)</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-success">Criar</button>
            </div>
        </form>
    </div>
</div>


{{-- ============= MODAIS: EDITAR & EXCLUIR ============= --}}
@foreach ($tags as $tag)

    {{-- EDITAR --}}
    <div class="modal fade" id="editTagModal{{ $tag->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('tags.update', $tag->id) }}" class="modal-content">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Editar Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nome da Tag</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ $tag->name }}" required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="description" class="form-control" rows="3">{{ $tag->description }}</textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>

            </form>
        </div>
    </div>


    {{-- EXCLUIR --}}
    <div class="modal fade" id="deleteTagModal{{ $tag->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('tags.destroy', $tag->id) }}" class="modal-content">
                @csrf
                @method('DELETE')

                <div class="modal-header">
                    <h5 class="modal-title">Excluir Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    Tem certeza que deseja excluir a tag <strong>{{ $tag->name }}</strong>?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </div>

            </form>
        </div>
    </div>

@endforeach


@endsection
