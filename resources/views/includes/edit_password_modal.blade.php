<div class="modal fade" id="editPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('profile.updatePassword') }}" class="modal-content">
            @csrf
            @method('PATCH')

            <div class="modal-header">
                <h5 class="modal-title">Alterar Senha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Senha atual:</label>
                    <input
                        type="password"
                        class="form-control"
                        name="current_password"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nova senha:</label>
                    <input
                        type="password"
                        class="form-control"
                        name="password"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirmar nova senha:</label>
                    <input
                        type="password"
                        class="form-control"
                        name="password_confirmation"
                        required>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary">Alterar Senha</button>
            </div>

        </form>
    </div>
</div>
