@extends('layouts.main')

@section('content')
    <style>
        .admin-dashboard {
            display: flex;
            gap: 1.5rem;
        }

        .dashboard-sidebar {
            width: 220px;
        }

        .dashboard-link {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: .5rem;
            padding: .5rem .75rem;
            border: none;
            background: #f5f5f5;
            cursor: pointer;
        }

        .dashboard-link.active {
            background: #e0e0e0;
            font-weight: bold;
        }

        .dashboard-panel {
            display: none;
        }

        .dashboard-panel.active {
            display: block;
        }
    </style>
    <div class="admin-dashboard">

        {{-- SIDEBAR INTERNA --}}
        @include('admin.dashboard._sidebar')

        {{-- CONTEÚDO --}}
        <div class="dashboard-content">

            <div id="panel-orders" class="dashboard-panel active">
                @include('admin.dashboard.panels.orders')
            </div>

            <div id="panel-moderation" class="dashboard-panel">
                @include('admin.dashboard.panels.moderation')
            </div>

            <div id="panel-tags" class="dashboard-panel">
                @include('admin.dashboard.panels.tags')
            </div>

            <div id="panel-users" class="dashboard-panel">
                @include('admin.dashboard.panels.users')
            </div>

        </div>
    </div>

    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-body" id="orderModalContent">
                    <div class="text-center p-5 text-muted">
                        Carregando pedido...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="whatsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Enviar mensagem ao cliente</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label for="whatsMsg" class="form-label">Mensagem:</label>
                    <textarea id="whatsMsg" class="form-control" rows="8"></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" id="btnCopyWhats">
                        Copiar mensagem
                    </button>

                    <a id="btnOpenWhats" class="btn btn-success" target="_blank">
                        Abrir WhatsApp
                    </a>
                </div>

            </div>
        </div>
    </div>


    <script>
        document.querySelectorAll('.dashboard-link').forEach(btn => {
            btn.addEventListener('click', () => {

                document.querySelectorAll('.dashboard-link')
                    .forEach(b => b.classList.remove('active'));

                document.querySelectorAll('.dashboard-panel')
                    .forEach(p => p.classList.remove('active'));

                btn.classList.add('active');
                document
                    .getElementById('panel-' + btn.dataset.panel)
                    .classList.add('active');
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

                    console.group('[AJAX MARK PAID]');
                    console.log('Resposta completa:', data);
                    console.log('success:', data.success);
                    console.log('html existe?', 'html' in data);
                    console.log('html tipo:', typeof data.html);
                    console.log('html tamanho:', data.html ? data.html.length : 'NULL');
                    console.log('whatsappMessage:', data.whatsappMessage);
                    console.log('whatsappNumber:', data.whatsappNumber);
                    console.groupEnd();

                    if (!data.success) {
                        alert(data.message || 'Erro ao marcar como pago');
                        btn.disabled = false;
                        btn.innerText = originalText;
                        return;
                    }

                    // ⚠️ TESTE CRÍTICO
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
@endsection
