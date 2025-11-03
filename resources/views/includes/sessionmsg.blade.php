@if (session('msg'))
    <div
        id="success-alert"
        class="alert alert-primary alert-dismissible fade show auto-close"
        role="alert"
    >
        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>
        <strong>Mensagem:</strong> {{ session('msg') }}
    </div>

    <script>
        // Inicializa o componente de alerta do Bootstrap
        document.querySelectorAll(".alert").forEach(alert => {
            new bootstrap.Alert(alert);
        });

        // Faz o alerta desaparecer depois de 4 segundos
        setTimeout(() => {
            const alertEl = document.getElementById("success-alert");
            if (alertEl) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
                bsAlert.close();
            }
        }, 4000);
    </script>
@endif
