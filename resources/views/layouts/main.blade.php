<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">

<head>
    @include('includes.styles')
    <script>
        (function() {
            const savedTheme = localStorage.getItem("theme") || "dark";
            document.documentElement.setAttribute("data-theme", savedTheme);
        })();
    </script>
    <title>S.I.C.A - Sistema de Informação Sobre Chás Avaliados</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_sica.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')
</head>

<body>
    <div class="main d-flex flex-column min-vh-100">
        @include('includes.header')
        @include('includes.sessionmsg')

        <main class="flex-fill">
            @yield('content')
        </main>

        <div id="myFooter"
            class="myFooter mx-auto col-sm-12 col-md-12 col-lg-12 col-xl-10 border-top border-white py-4 overflow-hidden">
            @include('includes.footer')
        </div>
    </div>


    {{-- Modal do leitor --}}
    <div id="qrModal" class="qr-modal d-none">
        <div class="qr-modal-content">
            <span id="closeQrModal" class="close-btn">&times;</span>
            <h5 class="mb-3 text-center">Leitor de QR Code</h5>
            <div id="reader" style="width: 280px; margin: auto;"></div>
            <div id="scanResult" class="text-center text-success mt-3 fw-semibold"></div>
        </div>
    </div>
    @include('includes.mobile-nav')
    @include('includes.scripts')
    @if (Auth::check() && Auth::user()->user_lvl === 'admin')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const badge = document.getElementById('admin-notification-badge');
                if (!badge) return; // evita erros caso o elemento não exista

                async function updateAdminBadge() {
                    try {
                        const res = await fetch('{{ route('admin.notifications') }}');
                        const data = await res.json();

                        if (data.total && data.total > 0) {
                            badge.textContent = data.total;
                            badge.style.display = 'inline-flex';
                        } else {
                            badge.style.display = 'none';
                        }
                    } catch (err) {
                        console.error('Erro ao atualizar badge do admin:', err);
                    }
                }

                // Atualiza imediatamente ao carregar a página
                updateAdminBadge();

                // Atualiza a cada 30 segundos
                setInterval(updateAdminBadge, 30000);

            });
        </script>
    @endif

</body>
</html>
