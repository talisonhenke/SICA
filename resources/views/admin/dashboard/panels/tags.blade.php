{{-- PAINEL: TAGS --}}

@include('includes.toast')

<style>
    :root {
        --primary-color: #4CAF50;
        --primary-dark: #2E7D32;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Tags</h4>

    <button class="btn btn-success"
        data-bs-toggle="modal"
        data-bs-target="#createTagModal">
        + Nova Tag
    </button>
</div>


<div class="card p-3">

    {{-- DESKTOP --}}
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
                            <button class="btn btn-sm btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#editTagModal{{ $tag->id }}">
                                Editar
                            </button>

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

    {{-- MOBILE --}}
    <div class="d-md-none">
        @foreach ($tags as $tag)
            <div class="border rounded p-3 mb-3 bg-light">
                <div><strong>ID:</strong> {{ $tag->id }}</div>
                <div><strong>Nome:</strong> {{ $tag->name }}</div>
                <div><strong>Descrição:</strong> {{ Str::limit($tag->description, 200) }}</div>

                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary btn-sm w-50"
                        data-bs-toggle="modal"
                        data-bs-target="#editTagModal{{ $tag->id }}">
                        Editar
                    </button>

                    <button class="btn btn-danger btn-sm w-50"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteTagModal{{ $tag->id }}">
                        Excluir
                    </button>
                </div>
            </div>
        @endforeach
    </div>

</div>

<div class="modal fade" id="createTagModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content js-create-tag"
              data-url="{{ route('admin.dashboard.panels.tags.store') }}">

            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Nova Tag</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Nome da Tag</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           required
                           maxlength="255">
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="description"
                              class="form-control"
                              rows="3"></textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-success">
                    Criar
                </button>
            </div>

        </form>
    </div>
</div>

@foreach ($tags as $tag)

<div class="modal fade" id="editTagModal{{ $tag->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content js-edit-tag"
              data-url="{{ route('admin.dashboard.panels.tags.update', $tag->id) }}">

            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Editar Tag</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ $tag->name }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="description"
                              class="form-control"
                              rows="3">{{ $tag->description }}</textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-primary">
                    Salvar
                </button>
            </div>

        </form>
    </div>
</div>

<div class="modal fade" id="deleteTagModal{{ $tag->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Excluir Tag</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Tem certeza que deseja excluir a tag
                <strong>{{ $tag->name }}</strong>?
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-danger js-delete-tag"
                        data-url="{{ route('admin.dashboard.panels.tags.destroy', $tag->id) }}">
                    Excluir
                </button>
            </div>

        </div>
    </div>
</div>

@endforeach
