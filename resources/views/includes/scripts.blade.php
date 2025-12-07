<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
    integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
</script>

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
        const fadeInterval = setInterval(function() {
            if (opacity > 0) {
                opacity -= 0.1;
                element.style.opacity = opacity;
            } else {
                clearInterval(fadeInterval);
                // Step 2: Slide up the element
                let height = element.offsetHeight;
                const slideInterval = setInterval(function() {
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
    setTimeout(function() {
        autoCloseElements.forEach(function(element) {
            fadeAndSlide(element);
        });
    }, 5000);
</script>

{{-- Toast editar perfil --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toastEl = document.getElementById('successToast');
        if (toastEl) {
            var toast = new bootstrap.Toast(toastEl, {
                delay: 5000 // 5 segundos
            });
            toast.show();
        }
    });
</script>

{{-- Botões de submit com delay para evitar cliques fantasma --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Seleciona todos os botões de submit com o ID "submitBtn"
        const buttons = document.querySelectorAll('#submitBtn');

        buttons.forEach(button => {
            const form = button.closest('form');
            if (!form) return; // ignora se não estiver dentro de um formulário

            form.addEventListener('submit', function() {
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
{{-- <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script> --}}

<script src="https://unpkg.com/html5-qrcode"></script>
{{-- <script src="../../js/html5_qrcode.min.js"></script> --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const qrBtn = document.getElementById("openQrModal"); // <-- Agora usa o menu
        const qrModal = document.getElementById("qrModal");
        const closeModal = document.getElementById("closeQrModal");
        const scanResult = document.getElementById("scanResult");
        const baseDomain = "{{ url('/') }}";
        // console.log("Domínio base:" + baseDomain);

        let html5QrCode;

        qrBtn.addEventListener("click", (e) => {
            e.preventDefault(); // Evita scroll ao topo do site
            qrModal.classList.remove("d-none");
            startScanner();
        });

        closeModal.addEventListener("click", () => {
            qrModal.classList.add("d-none");
            stopScanner();
        });

        function startScanner() {
            html5QrCode = new Html5Qrcode("reader");

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    const backCamera = devices.find(device =>
                        device.label.toLowerCase().includes('back') ||
                        device.label.toLowerCase().includes('rear') ||
                        device.label.toLowerCase().includes('environment')
                    );
                    const cameraId = backCamera ? backCamera.id : devices[0].id;

                    html5QrCode.start({
                            deviceId: {
                                exact: cameraId
                            }
                        }, {
                            fps: 10,
                            qrbox: {
                                width: 250,
                                height: 250
                            }
                        },
                        decodedText => {
                            // console.log(decodedText);
                            if (decodedText.startsWith(baseDomain)) {
                                scanResult.innerHTML = `✅ Código reconhecido!<br>${decodedText}`;
                                setTimeout(() => window.location.href = decodedText, 1000);
                            } else {
                                scanResult.innerHTML = `⚠️ QR Code inválido para este sistema.`;
                            }
                        }
                    );
                }
            }).catch(err => {
                scanResult.innerHTML =
                    `<span style="color:red;">❌ Erro ao acessar câmera: ${err}</span>`;
            });
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().catch(err => console.error("Erro ao parar scanner:", err));
            }
        }
    });
</script>


{{-- Troca de cores tema claro e escuro --}}

<script>
    function applyIconMode(theme) {
        const icons = document.querySelectorAll("#themeIcon, #themeIconMobile");

        icons.forEach(icon => {
            if (!icon) return;

            icon.classList.add("theme-animated");

            setTimeout(() => icon.classList.remove("theme-animated"), 300);

            if (theme === "dark") {
                icon.classList.remove("bi-sun-fill");
                icon.classList.add("bi-moon-fill");
            } else {
                icon.classList.remove("bi-moon-fill");
                icon.classList.add("bi-sun-fill");
            }
        });
    }

    document.addEventListener("DOMContentLoaded", () => {

        const html = document.documentElement;

        const btnDesktop = document.getElementById("themeToggleBtn");
        const btnMobile = document.getElementById("themeToggleBtnMobile");

        // 1️⃣ Ler tema salvo
        let savedTheme = localStorage.getItem("theme") || "light";
        if (savedTheme !== "light" && savedTheme !== "dark") {
            savedTheme = "light";
        }

        // 2️⃣ Aplicar tema ao carregar
        html.setAttribute("data-theme", savedTheme);
        applyIconMode(savedTheme);

        // 3️⃣ Função de alternância
        function toggleTheme() {
            const current = html.getAttribute("data-theme");
            const newTheme = current === "light" ? "dark" : "light";

            html.setAttribute("data-theme", newTheme);
            localStorage.setItem("theme", newTheme);

            applyIconMode(newTheme);
        }

        // 4️⃣ Eventos
        if (btnDesktop) btnDesktop.addEventListener("click", toggleTheme);
        if (btnMobile) btnMobile.addEventListener("click", toggleTheme);

    });
</script>


{{-- tribute (menções) --}}
<script src="{{ asset('vendor/tribute/tribute.min.js') }}"></script>

<!-- toast sem session  -->
<script>
    function showSessionToast(message) {
        // Apaga qualquer alerta anterior
        const old = document.getElementById("success-alert");
        if (old) old.remove();

        // Cria div do alerta
        const alertDiv = document.createElement("div");
        alertDiv.id = "success-alert";
        alertDiv.className = "alert alert-primary alert-dismissible fade show auto-close";
        alertDiv.role = "alert";
        alertDiv.innerHTML = `
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <strong>Mensagem:</strong> ${message}
    `;

        // Adiciona ao topo da página (ou onde preferir)
        document.body.prepend(alertDiv);

        // Inicializa bootstrap alert
        new bootstrap.Alert(alertDiv);

        // Auto-close após 4s
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alertDiv);
            bsAlert.close();
        }, 4000);
    }
</script>

{{-- Verificação de novos comentários e pedidos --}}
<script>
    function checkModerationUpdates() {
        console.log('[MODERAÇÃO] Executando checkModerationUpdates()...');

        fetch('/api/check-updates')
            .then(res => {
                console.log('[MODERAÇÃO] Resposta bruta:', res);
                return res.json();
            })
            .then(data => {
                console.log('[MODERAÇÃO] JSON recebido:', data);

                const badge = document.getElementById('moderationBadge');

                if (!badge) {
                    console.warn('[MODERAÇÃO] Não encontrou #moderationBadge no DOM.');
                    return;
                }

                const count = data.moderation ?? 0;
                console.log('[MODERAÇÃO] Contagem:', count);

                if (count > 0) {
                    badge.classList.remove('d-none');
                    badge.innerText = count;
                } else {
                    badge.classList.add('d-none');
                }
            })
            .catch(err => {
                console.error('[MODERAÇÃO] Erro ao buscar updates:', err);
            });
    }

    function checkOrderUpdates() {
        console.log('[PEDIDOS] Executando checkOrderUpdates()...');

        fetch('/api/check-updates')
            .then(res => {
                console.log('[PEDIDOS] Resposta bruta:', res);
                return res.json();
            })
            .then(data => {
                console.log('[PEDIDOS] JSON recebido:', data);

                const badge = document.getElementById('ordersBadge');

                if (!badge) {
                    console.warn('[PEDIDOS] Não encontrou #ordersBadge no DOM.');
                    return;
                }

                const count = data.orders ?? 0;
                console.log('[PEDIDOS] Contagem:', count);

                if (count > 0) {
                    badge.classList.remove('d-none');
                    badge.innerText = count;
                } else {
                    badge.classList.add('d-none');
                }

            })
            .catch(err => {
                console.error('[PEDIDOS] Erro ao buscar updates:', err);
            });
    }

    // Primeiro carregamento
    console.log('Executando checagem inicial...');
    checkModerationUpdates();
    checkOrderUpdates();

    // Execução periódica
    setInterval(() => {
        console.log('Executando checagem periódica...');
        checkModerationUpdates();
        checkOrderUpdates();
    }, 6000); // 6 segundos para testar
</script>
