<div class="modal fade" id="editPhoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('profile.updatePhone') }}" class="modal-content">
            @csrf
            @method('PATCH')

            <div class="modal-header">
                <h5 class="modal-title">Editar Telefone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Telefone:</label>
                    <input
                        type="text"
                        class="form-control"
                        id="phoneInput"
                        name="phone_number"
                        maxlength="16"
                        value="{{ old('phone', $user->phone ? preg_replace('/(\d{2})(\d{1})(\d{4})(\d{4})/', '($1) $2 $3-$4', $user->phone) : '') }}"
                        placeholder="(xx) x xxxx-xxxx"
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('phoneInput');

    input.addEventListener('input', function () {
        let digits = input.value.replace(/\D/g, "");

        // Limita a 11 dígitos
        if (digits.length > 11) digits = digits.slice(0, 11);

        // Se estiver apagando tudo
        if (digits.length === 0) {
            input.value = "";
            return;
        }

        let formatted = "";

        // (XX
        if (digits.length <= 2) {
            formatted = `(${digits}`;
        }
        // (XX) Y
        else if (digits.length <= 3) {
            formatted = `(${digits.slice(0,2)}) ${digits.slice(2)}`;
        }
        // (XX) Y ZZZZ
        else if (digits.length <= 7) {
            formatted = `(${digits.slice(0,2)}) ${digits.slice(2,3)} ${digits.slice(3)}`;
        }
        // (XX) Y ZZZZ-W
        else {
            formatted = `(${digits.slice(0,2)}) ${digits.slice(2,3)} ${digits.slice(3,7)}-${digits.slice(7)}`;
        }

        input.value = formatted;
    });
});
</script>


