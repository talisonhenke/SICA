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
			<strong>Mensagem</strong> {{ session('msg') }}
		</div>
		
		<script>
			var alertList = document.querySelectorAll(".alert");
			alertList.forEach(function (alert) {
				new bootstrap.Alert(alert);
			});
		</script>
		
	@endif