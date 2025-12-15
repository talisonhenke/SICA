@extends('layouts.main')

@section('content')
    <style>
        body {
            background: #f4f6f9;
        }

        .admin-dashboard {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: calc(100vh - 70px);
            gap: 1.5rem;
            padding: 1.5rem;
        }

        /* SIDEBAR */
        .dashboard-sidebar {
            background-color: var(--color-surface-primary);
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .dashboard-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .75rem 1rem;
            border-radius: 8px;
            border: none;
            background: transparent;
            font-weight: 500;
            color: #444;
            transition: .2s;
            cursor: pointer;
        }

        .dashboard-link:hover {
            background: #f1f3f5;
        }

        .dashboard-link.active {
            background: #e9ecef;
            font-weight: 600;
            color: #000;
        }

        /* CONTEÚDO */
        .dashboard-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            background-color: var(--color-surface-primary);
            border-radius: 12px;
        }

        .dashboard-panel {
            display: none;
            animation: fadeIn .25s ease-in-out;
        }

        .dashboard-panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="admin-dashboard">

        {{-- SIDEBAR --}}
        @include('admin.dashboard._sidebar')

        {{-- CONTEÚDO --}}
        <div id="dashboard-panel" class="dashboard-content">

            <div id="panel-init" class="dashboard-panel">
                <div class="text-muted p-4">
                    Selecione um painel no menu.
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PEDIDO --}}
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4">
                <div class="modal-body" id="orderModalContent">
                    <div class="text-center p-5 text-muted">Carregando pedido...</div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL WHATSAPP --}}
    <div class="modal fade" id="whatsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">

                <div class="modal-header">
                    <h5 class="modal-title">Mensagem para o cliente</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Mensagem:</label>
                    <textarea id="whatsMsg" class="form-control" rows="8"></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" id="btnCopyWhats">
                        Copiar
                    </button>

                    <a id="btnOpenWhats" class="btn btn-success" target="_blank">
                        Abrir WhatsApp
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- FILTRO DE PEDIDOS  --}}

    <script>
        document.addEventListener('click', function(e) {

            const filterButton = e.target.closest('.btn-filter');
            if (!filterButton) return;

            const status = filterButton.dataset.status;

            // Remove active de todos
            document.querySelectorAll('.btn-filter').forEach(btn => {
                btn.classList.remove('active');
            });

            // Marca o atual
            filterButton.classList.add('active');

            // Carrega painel com filtro
            loadPanel('orders', 'status=' + status);

        });
    </script>

    {{-- admin/dashboard/index.blade.php --}}
    <script>
        function loadPanel(panel, query = '') {
            fetch(`/admin/panels/${panel}?${query}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('dashboard-panel').innerHTML = html;
                })
                .catch(() => {
                    document.getElementById('dashboard-panel').innerHTML =
                        '<div class="p-4 text-danger">Erro ao carregar painel.</div>';
                });
        }
    </script>

    <script>
        document.addEventListener('change', function(e) {

            if (e.target && e.target.id === 'filterSelect') {

                const filter = encodeURIComponent(e.target.value);

                loadPanel('moderation', 'filter=' + filter);
            }

        });
    </script>
    {{-- Carrega painel de pedidos ao iniciar o index do dashboard --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // 1️⃣ Carrega o painel inicial (Orders)
            loadPanel('orders');

            // 2️⃣ Mantém o comportamento dos botões
            document.querySelectorAll('.dashboard-link').forEach(button => {
                button.addEventListener('click', () => {
                    const panel = button.dataset.panel;
                    if (!panel) return;
                    loadPanel(panel);
                });
            });

        });
    </script>


    {{-- Carregar paineis através do menu lateral  --}}
    <script>
        document.querySelectorAll('.dashboard-link').forEach(button => {
            button.addEventListener('click', () => {
                const panel = button.dataset.panel;

                if (!panel) return;

                loadPanel(panel);
            });
        });
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}"></script>

    <script>
        // Estado global do mapa
        window.orderMapInstance = null;

        // Função única e global
        window.initOrderMap = function() {
            console.log('[Map] INIT');

            const mapEl = document.getElementById('orderMap');

            if (!mapEl) {
                console.warn('[Map] #orderMap não encontrado');
                return;
            }

            const lat = mapEl.dataset.lat;
            const lng = mapEl.dataset.lng;

            if (!lat || !lng) {
                console.warn('[Map] Coordenadas ausentes');
                return;
            }

            const position = {
                lat: Number(lat),
                lng: Number(lng)
            };

            // Cria (ou recria) o mapa
            window.orderMapInstance = new google.maps.Map(mapEl, {
                center: position,
                zoom: 17,
                streetViewControl: false,
                fullscreenControl: false,
                mapTypeControl: false,
                draggable: false
            });

            new google.maps.Marker({
                position,
                map: window.orderMapInstance
            });

            // Força repaint real
            setTimeout(() => {
                google.maps.event.trigger(window.orderMapInstance, 'resize');
                window.orderMapInstance.setCenter(position);
            }, 200);
        };

        // Quando o modal ABRE
        document
            .getElementById('orderModal')
            .addEventListener('shown.bs.modal', function() {
                window.initOrderMap();
            });
    </script>

    <script>
        function updateOrderModal(html) {
            const container = document.getElementById('orderModalContent');

            if (!container) {
                console.warn('[Modal] #orderModalContent não encontrado');
                return;
            }

            container.innerHTML = html;

            // Aguarda o DOM renderizar
            requestAnimationFrame(() => {
                const mapEl = container.querySelector('#orderMap');

                if (!mapEl) {
                    console.info('[Map] HTML atualizado sem mapa — init ignorado');
                    return;
                }

                if (typeof window.initOrderMap === 'function') {
                    window.initOrderMap();
                }
            });
        }
    </script>


    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.js-open-order');
            if (!btn) return;

            const url = btn.dataset.url;
            console.log("URL ", url);

            fetch(url)
                .then(res => {
                    if (!res.ok) throw new Error('Erro ao carregar pedido');
                    return res.text();
                })
                .then(html => {
                    document.getElementById('orderModalContent').innerHTML = html;

                    const modal = new bootstrap.Modal(
                        document.getElementById('orderModal')
                    );
                    modal.show();
                })
                .catch(() => {
                    document.getElementById('orderModalContent').innerHTML =
                        '<div class="p-4 text-danger">Erro ao carregar pedido.</div>';
                });
        });
    </script>

    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.js-mark-paid');
            if (!btn) return;

            if (!confirm('Confirma que este pedido foi PAGO?')) return;

            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Processando...';

            fetch(btn.dataset.url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {

                    if (!data.success) {
                        alert(data.message || 'Erro ao marcar como pago');
                        btn.disabled = false;
                        btn.innerText = originalText;
                        return;
                    }

                    if (!data.html) {
                        console.error('[ERRO] data.html não veio do backend');
                        btn.disabled = false;
                        btn.innerText = originalText;
                        return;
                    }

                    updateOrderModal(data.html);

                    if (data.whatsappMessage && data.whatsappNumber) {
                        const msgEl = document.getElementById('whatsMsg');
                        const linkEl = document.getElementById('btnOpenWhats');

                        if (msgEl && linkEl) {
                            msgEl.value = data.whatsappMessage;
                            linkEl.href = `https://wa.me/${data.whatsappNumber}`;

                            const whatsModalEl = document.getElementById('whatsModal');
                            if (whatsModalEl) {
                                new bootstrap.Modal(whatsModalEl).show();
                            }
                        }
                    }

                })
                .catch(() => {
                    alert('Erro inesperado.');
                    btn.disabled = false;
                    btn.innerText = originalText;
                });
        });
    </script>


    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.js-mark-shipped');
            if (!btn) return;

            if (!confirm('Confirma que este pedido foi ENVIADO?')) return;

            btn.disabled = true;
            btn.innerText = 'Processando...';

            fetch(btn.dataset.url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'Erro ao enviar pedido');
                        btn.disabled = false;
                        btn.innerText = 'Enviar pedido';
                        return;
                    }

                    // ✅ USO PADRÃO
                    updateOrderModal(data.html);

                    if (data.whatsappMessage && data.whatsappNumber) {
                        const msgEl = document.getElementById('whatsMsg');
                        const linkEl = document.getElementById('btnOpenWhats');

                        if (msgEl && linkEl) {
                            msgEl.value = data.whatsappMessage;
                            linkEl.href = `https://wa.me/${data.whatsappNumber}`;

                            const whatsModalEl = document.getElementById('whatsModal');
                            if (whatsModalEl) {
                                new bootstrap.Modal(whatsModalEl).show();
                            }
                        }
                    }
                })
                .catch(() => {
                    alert('Erro inesperado.');
                    btn.disabled = false;
                    btn.innerText = 'Enviar pedido';
                });
        });
    </script>


    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.js-cancel-order');
            if (!btn) return;

            const confirmed = confirm(
                'Tem certeza que deseja CANCELAR este pedido?\n\nEssa ação não poderá ser desfeita.'
            );

            if (!confirmed) return;

            btn.disabled = true;
            btn.innerText = 'Cancelando...';

            fetch(btn.dataset.url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'Erro ao cancelar pedido.');
                        btn.disabled = false;
                        btn.innerText = 'Cancelar pedido';
                        return;
                    }

                    // Atualiza o modal inteiro
                    document.getElementById('orderModalContent').innerHTML = data.html;

                    // Re-inicializa mapa (se existir)
                    setTimeout(() => {
                        if (typeof initOrderMap === 'function') {
                            initOrderMap();
                        }
                    }, 300);
                })
                .catch(() => {
                    alert('Erro inesperado.');
                    btn.disabled = false;
                    btn.innerText = 'Cancelar pedido';
                });
        });
    </script>


    <script>
        document.addEventListener('click', function(e) {

            const btn = e.target.closest('.js-open-whatsapp');
            if (!btn) return;

            const msg = btn.dataset.message;
            const phone = btn.dataset.phone;

            if (!msg || !phone) {
                alert('Mensagem indisponível.');
                return;
            }

            document.getElementById('whatsMsg').value = msg;
            document.getElementById('btnOpenWhats').href = `https://wa.me/${phone}`;

            const modal = new bootstrap.Modal(
                document.getElementById('whatsModal')
            );

            modal.show();
        });
    </script>


    <script>
        document.getElementById('btnCopyWhats')
            .addEventListener('click', function() {

                const textarea = document.getElementById('whatsMsg');
                textarea.select();
                textarea.setSelectionRange(0, 99999);

                navigator.clipboard.writeText(textarea.value);

                alert('Mensagem copiada!');
            });
    </script>

    <script>
        document.addEventListener('click', async function(e) {

            const actionButton =
                e.target.closest('.js-moderate-delete') ||
                e.target.closest('.js-allow-comment') ||
                e.target.closest('.js-block-user');

            if (!actionButton) return;

            e.preventDefault();

            let confirmMessage = '';

            if (actionButton.classList.contains('js-moderate-delete')) {
                confirmMessage = 'Excluir o comentário e aplicar STRIKE ao usuário?';
            }

            if (actionButton.classList.contains('js-allow-comment')) {
                confirmMessage = 'Deseja permitir este comentário?';
            }

            if (actionButton.classList.contains('js-block-user')) {
                confirmMessage = 'Bloquear este usuário definitivamente para comentários?';
            }

            if (!confirm(confirmMessage)) return;

            const modalElement = actionButton.closest('.modal');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);

            const response = await fetch(actionButton.dataset.url, {
                method: actionButton.classList.contains('js-moderate-delete') ? 'DELETE' : 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!data.success) {
                alert(data.message || 'Erro ao executar ação.');
                return;
            }

            // 1️⃣ Fecha o modal corretamente
            modalInstance.hide();

            // 2️⃣ Só recarrega o painel depois que o Bootstrap limpou tudo
            modalElement.addEventListener('hidden.bs.modal', function() {
                loadPanel('moderation');
            }, {
                once: true
            });

        });
    </script>

    {{-- PAINEL USUÁRIOS  --}}

    <script>
        document.addEventListener('change', async function(e) {

            const select = e.target.closest('.js-user-level');
            if (!select) return;

            const previousValue = select.dataset.previousValue ?? select.value;
            const newValue = select.value;

            const url = select.dataset.url;
            const username = select.dataset.username;

            const actionText = newValue === 'admin' ?
                `Deseja tornar ${username} administrador?` :
                `Deseja remover privilégios de administrador de ${username}?`;

            if (!confirm(actionText)) {
                select.value = previousValue;
                return;
            }

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_lvl: newValue
                })
            });

            const data = await response.json();

            if (!data.success) {
                alert(data.message || 'Erro ao atualizar nível.');
                select.value = previousValue;
                return;
            }

            // Atualiza o valor salvo
            select.dataset.previousValue = newValue;
        });
    </script>



    <script>
        /*
        |--------------------------------------------------------------------------
        | CREATE TAG
        |--------------------------------------------------------------------------
        */
        document.addEventListener('submit', function(e) {

            if (!e.target.classList.contains('js-create-tag')) return;

            e.preventDefault();

            const form = e.target;
            const url = form.dataset.url;
            const data = new FormData(form);

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]').content
                    },
                    body: data
                })
                .then(res => res.json())
                .then(() => {
                    bootstrap.Modal.getInstance(
                        document.getElementById('createTagModal')
                    ).hide();

                    loadPanel('tags'); // 🔥 recarrega o painel
                });
        });
    </script>

    <script>
        document.addEventListener('submit', function(e) {

            if (!e.target.classList.contains('js-edit-tag')) return;

            e.preventDefault();

            const form = e.target;
            const url = form.dataset.url;
            const data = new FormData(form);

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: data
                })
                .then(res => res.json())
                .then(() => {
                    bootstrap.Modal.getInstance(
                        form.closest('.modal')
                    ).hide();

                    loadPanel('tags');
                });
        });
    </script>

    <script>
        document.addEventListener('click', function(e) {

            if (!e.target.classList.contains('js-delete-tag')) return;

            const url = e.target.dataset.url;

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': 'DELETE'
                    }
                })
                .then(res => res.json())
                .then(() => {
                    bootstrap.Modal.getInstance(
                        e.target.closest('.modal')
                    ).hide();

                    loadPanel('tags');
                });
        });
    </script>
@endsection
