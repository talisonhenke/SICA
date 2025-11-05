<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggler = document.querySelector('.navbar-toggler');
    const icon = toggler.querySelector('.menu-button');
    const menu = document.getElementById(toggler.getAttribute('data-bs-target').substring(1));

    // Evento quando o menu começa a abrir
    menu.addEventListener('show.bs.collapse', function() {
        icon.classList.remove('bi-list');
        icon.classList.add('bi-x-circle');
    });

    // Evento quando o menu começa a fechar
    menu.addEventListener('hide.bs.collapse', function() {
        icon.classList.remove('bi-x-circle');
        icon.classList.add('bi-list');
    });
});
</script>

{{-- auto close flash messages --}}
<script>
	// Get all elements with class "auto-close"
	const autoCloseElements = document.querySelectorAll(".auto-close");

	// Define a function to handle the fading and sliding animation
	function fadeAndSlide(element) {
	const fadeDuration = 500;
	const slideDuration = 500;
	
	// Step 1: Fade out the element
	let opacity = 1;
	const fadeInterval = setInterval(function () {
		if (opacity > 0) {
		opacity -= 0.1;
		element.style.opacity = opacity;
		} else {
		clearInterval(fadeInterval);
		// Step 2: Slide up the element
		let height = element.offsetHeight;
		const slideInterval = setInterval(function () {
			if (height > 0) {
			height -= 10;
			element.style.height = height + "px";
			} else {
			clearInterval(slideInterval);
			// Step 3: Remove the element from the DOM
			element.parentNode.removeChild(element);
			}
		}, slideDuration / 10);
		}
	}, fadeDuration / 10);
	}

	// Set a timeout to execute the animation after 5000 milliseconds (5 seconds)
	setTimeout(function () {
	autoCloseElements.forEach(function (element) {
		fadeAndSlide(element);
	});
	}, 5000);
</script>

{{-- Toast editar perfil --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    var toastEl = document.getElementById('successToast');
    if(toastEl) {
        var toast = new bootstrap.Toast(toastEl, {
            delay: 5000 // 5 segundos
        });
        toast.show();
    }
});
</script>

{{-- Botões de submit com delay para evitar cliques fantasma --}}
<script>
	document.addEventListener('DOMContentLoaded', function () {
    // Seleciona todos os botões de submit com o ID "submitBtn"
    const buttons = document.querySelectorAll('#submitBtn');

    buttons.forEach(button => {
        const form = button.closest('form');
        if (!form) return; // ignora se não estiver dentro de um formulário

        form.addEventListener('submit', function () {
            // Desativa o botão
            button.disabled = true;
            // Mostra texto com spinner (animação de carregamento)
            button.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Salvando...
            `;

            // Reativa após 8s (caso o servidor demore ou ocorra erro)
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = 'Salvar Tópico';
            }, 8000);
        });
    });
});
</script>

{{-- Biblioteca para leitura de QR Code --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

{{-- Script QR-CODE --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qrBtn = document.getElementById('qrScanBtn');
    const qrModal = document.getElementById('qrModal');
    const closeBtn = document.getElementById('closeQrModal');
    const scanResult = document.getElementById('scanResult');
    let html5QrCode = null;

    // 🔒 Domínio permitido — altere conforme o seu
    const allowedDomain = window.location.origin; 
    // Isso captura dinamicamente o domínio atual (ex: https://meusite.com)

    qrBtn.addEventListener('click', () => {
        qrModal.classList.remove('d-none');
        startScanner();
    });

    closeBtn.addEventListener('click', () => {
        stopScanner();
        qrModal.classList.add('d-none');
    });

    function startScanner() {
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: 250 };

        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                html5QrCode.start(
                    devices[0].id,
                    config,
                    qrCodeSuccessCallback,
                    qrCodeErrorCallback
                );
            } else {
                scanResult.textContent = "Nenhuma câmera encontrada 😕";
            }
        }).catch(err => {
            scanResult.textContent = "Erro ao acessar câmera: " + err;
        });
    }

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
            });
        }
    }

    function qrCodeSuccessCallback(decodedText) {
        stopScanner();

        // 🔍 Validação de link
        if (!decodedText.startsWith("http")) {
            scanResult.textContent = "Código inválido: não é um link.";
            return;
        }

        // 🔒 Verifica se o domínio é o mesmo do sistema
        if (!decodedText.startsWith(allowedDomain)) {
            scanResult.textContent = "QR Code de outro domínio. A leitura foi bloqueada.";
            return;
        }

        // ✅ Se for válido, redireciona
        scanResult.textContent = "QR Code reconhecido. Redirecionando...";
        setTimeout(() => {
            window.location.href = decodedText;
        }, 800);
    }

    function qrCodeErrorCallback(error) {
        // Erros de leitura ignorados (ocorrem naturalmente)
    }
});
</script>


