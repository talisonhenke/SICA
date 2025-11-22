<div class="modal fade" id="editNameModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('profile.updateName') }}" class="modal-content">
            @csrf
            @method('PATCH')

            <div class="modal-header">
                <h5 class="modal-title">Editar Nome</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Nome completo:</label>
                    <input
                        type="text"
                        class="form-control"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary">Salvar Alterações</button>
            </div>

        </form>
    </div>
</div>
