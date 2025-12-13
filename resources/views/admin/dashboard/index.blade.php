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

<script>
document.addEventListener('click', function (e) {
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
            new bootstrap.Modal(document.getElementById('orderModal')).show();
        })
        .catch(() => {
            document.getElementById('orderModalContent').innerHTML =
                '<div class="p-4 text-danger">Erro ao carregar pedido.</div>';
        });
});
</script>



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

@endsection
