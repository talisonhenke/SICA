@extends('layouts.main')
@section('meta')
    @php
        use Illuminate\Support\Facades\File;

        $path = public_path('images/plants/' . $plant->id);
        $files = File::files($path);

        $firstImage = count($files) > 0 ? 'images/plants/' . $plant->id . '/' . $files[0]->getFilename() : null;
    @endphp

    @if ($firstImage)
        <meta property="og:image" content="{{ asset($firstImage) }}">
    @endif

    <meta property="og:title" content="{{ $plant->popular_name }}">
    <meta property="og:description" content="{{ Str::limit($plant->observations, 150) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
@endsection

@section('content')
    <style>
        .text-info-custom {
            color: var(--color-text);
        }

        .article-container {
            max-width: 950px;
            margin: 2rem auto;
            background-color: var(--color-surface-primary);
            color: var(--color-text-dark);
            border-radius: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        .admin-options {
            background-color: var(--color-surface-secondary);
            border-radius: 0.75rem;
            padding: 1rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .admin-options h3 {
            color: var(--color-text-dark);
            margin-bottom: 0.8rem;
        }

        .admin-options .btn {
            margin: 0 0.25rem;
        }

        .article-header {
            border-bottom: 2px solid var(--color-accent);
            margin-bottom: 1.2rem;
            padding-bottom: 0.8rem;
        }

        .article-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-primary);
            text-transform: uppercase;
            margin-bottom: 0.2rem;
        }

        .article-body strong {
            color: var(--color-secondary);
        }

        .article-content p {
            line-height: 1.8;
            font-size: 1.05rem;
            color: var(--color-text);
            text-align: justify;
            margin-bottom: 1rem;
        }

        .article-content p.indent:first-child {
            text-indent: 2rem;
        }

        /* Carrossel */
        .plant-carousel-img {
            height: 420px;
            object-fit: cover;
            border-radius: 0.75rem;
        }

        .custom-carousel-btn {
            width: 55px;
            height: 55px;
            top: 50%;
            transform: translateY(-50%);
            background-color: var(--color-surface);
            border-radius: 50%;
            transition: background-color 0.3s;
            margin: 0 10px;
            z-index: 3;
            opacity: 1;
        }

        .custom-carousel-btn:hover {
            background-color: var(--color-success);
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            filter: invert(1);
            width: 25px;
            height: 25px;
        }

        .carousel-gradient-left,
        .carousel-gradient-right {
            position: absolute;
            top: 0;
            width: 70px;
            height: 100%;
            z-index: 2;
            /* abaixo dos botões (que estão em z-index: auto/3) */
            pointer-events: none;
        }

        .carousel-gradient-left {
            left: 0;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.6), transparent);
        }

        .carousel-gradient-right {
            right: 0;
            background: linear-gradient(to left, rgba(0, 0, 0, 0.6), transparent);
        }


        .thumbnail-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .thumbnail-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .thumbnail-img:hover,
        .thumbnail-img.active {
            border-color: var(--color-accent);
            transform: scale(1.05);
        }

        /* Seção de informações adicionais */
        .extra-info {
            margin-top: 2rem;
            background-color: var(--color-surface-secondary);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .extra-info h2 {
            text-align: center;
            color: var(--color-primary);
            margin-bottom: 1rem;
        }

        .extra-info strong {
            color: var(--color-secondary);
        }

        .items-list {
            list-style-type: disc;
            margin-left: 1.5rem;
            color: var(--color-text);
        }

        @media (max-width: 768px) {
            .plant-carousel-img {
                height: 250px;
            }

            .custom-carousel-btn {
                width: 40px;
                height: 40px;
            }

            .thumbnail-img {
                width: 55px;
                height: 55px;
            }
        }

        .related-product {
            background-color: var(--color-surface-secondary);
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin-top: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .related-title {
            text-align: center;
            margin-bottom: 1.2rem;
            color: var(--color-primary);
            font-weight: 700;
        }

        .related-card {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .related-image-wrapper {
            flex: 0 0 180px;
        }

        .related-image {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        .related-info {
            flex: 1;
        }

        .related-name a {
            color: var(--color-primary);
            font-size: 1.4rem;
            text-decoration: none;
        }

        .related-price {
            margin-top: 0.8rem;
            font-size: 1.1rem;
        }

        .related-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        @media (max-width: 768px) {
            .related-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .related-info {
                width: 100%;
            }
        }

        .header-actions {
            display: flex;
            gap: 0.6rem;
        }

        .share-btn {
            background: var(--color-surface-secondary);
            border: none;
            padding: 0.5rem 0.6rem;
            border-radius: 0.6rem;
            cursor: pointer;
            transition: 0.2s;
            font-size: 1.4rem;
        }

        .share-btn:hover {
            background: var(--color-accent);
            color: white;
        }

        body.modal-open {
            padding-right: 0 !important;
            overflow: hidden;
            /* mantém o bloqueio do scroll sem empurrar a página */
        }

        .comments-section {
            margin-top: 3rem;
        }

        .avatar-circle {
            width: 48px;
            height: 48px;
            background: var(--color-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 1.2rem;
            user-select: none;
        }

        .comment-box {
            background: var(--color-surface-primary);
        }

        .tag-tooltip {
            position: relative;
            display: inline-block;
            margin-right: 6px;
        }

        .tooltip-box {
            display: none;
            position: absolute;
            top: 28px;
            /* distancia do badge */
            left: 0;
            z-index: 9999;
            background: #fff;
            color: #333;
            padding: 10px;
            width: 220px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            font-size: 0.85rem;
            line-height: 1.2rem;
            border: 1px solid #ddd;
        }

        .tag-tooltip:hover .tooltip-box {
            display: block;
            animation: fadeIn 0.15s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-3px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="article-container">
        {{-- Opções do administrador --}}
        @if (Auth::check() && Auth::user()->user_lvl === 'admin')
            <div class="admin-options">
                <h3 class="text-info-custom">⚙️ Opções do Administrador</h3>
                <a href="{{ route('plants.edit', $plant->id) }}" class="btn btn-warning btn-sm">Editar</a>
                <form action="{{ route('plants.destroy', $plant->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Tem certeza que deseja excluir esta planta?')">
                        Excluir
                    </button>
                </form>
            </div>
        @endif

        {{-- Cabeçalho do artigo --}}
        <div class="article-header d-flex justify-content-between align-items-start">
            <div>
                <h1 class="article-title">{{ $plant->popular_name }}</h1>
                <small class="text-info-custom"><strong>Publicado em:</strong>
                    {{ $plant->created_at->format('d/m/Y') }}</small>
            </div>

            <div class="header-actions">
                <button class="share-btn" data-bs-toggle="modal" data-bs-target="#shareModal">
                    <i class="bi bi-share-fill"></i>
                </button>
            </div>
        </div>


        <div class="article-body">
            <p class="text-info-custom"><strong>Nome Científico:</strong> {{ $plant->scientific_name }}</p>
            <p class="text-info-custom"><strong>Nome Popular:</strong> {{ $plant->popular_name }}</p>

            {{-- Carrossel de imagens --}}
            @php
                $images = is_array($plant->images) ? $plant->images : json_decode($plant->images, true);
            @endphp

            @if (!empty($images) && count($images) > 0)
                <div class="mx-auto col-sm-10 col-lg-8 mb-4">
                    <div id="plantCarousel{{ $plant->id }}" class="carousel slide" data-bs-ride="false"
                        data-bs-interval="false">
                        <div class="carousel-inner">
                            @foreach ($images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ asset($image) }}" class="d-block w-100 plant-carousel-img"
                                        alt="Imagem da planta">
                                </div>
                            @endforeach
                        </div>

                        <!-- Gradientes laterais -->
                        <div class="carousel-gradient-left"></div>
                        <div class="carousel-gradient-right"></div>

                        <button class="carousel-control-prev custom-carousel-btn" type="button"
                            data-bs-target="#plantCarousel{{ $plant->id }}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next custom-carousel-btn" type="button"
                            data-bs-target="#plantCarousel{{ $plant->id }}" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>


                    {{-- Miniaturas --}}
                    <div class="thumbnail-container">
                        @foreach ($images as $index => $image)
                            <img src="{{ asset($image) }}" class="thumbnail-img {{ $index === 0 ? 'active' : '' }}"
                                data-bs-target="#plantCarousel{{ $plant->id }}" data-bs-slide-to="{{ $index }}">
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Corpo do artigo --}}
            <div class="article-content">
                @php
                    $paragraphs = preg_split('/\r\n|\r|\n/', $plant->popular_use);
                    $paragraphs = array_filter($paragraphs, fn($p) => trim($p) !== '');
                @endphp

                @foreach ($paragraphs as $p)
                    <p class="indent">{{ $p }}</p>
                @endforeach
            </div>
            {{-- Produto relacionado --}}
            @if (isset($product) && $product)
                <div class="related-product mt-4">
                    <h2 class="related-title">Produto relacionado</h2>

                    <div class="related-card">

                        <div class="related-image-wrapper">
                            <a href="{{ route('products.show', $product->id) }}">
                                <img src="{{ asset($product->image) }}" alt="Imagem do produto" class="related-image">
                            </a>
                        </div>

                        <div class="related-info">
                            <h3 class="related-name">
                                <a href="{{ route('products.show', $product->id) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            <p class="related-description text-info-custom">
                                {{ Str::limit($product->description, 120) }}
                            </p>

                            <p class="related-price text-info-custom"><strong>Preço:</strong> R$
                                {{ number_format($product->price, 2, ',', '.') }}</p>

                            <div class="related-actions">
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary btn-sm">
                                    Ver detalhes
                                </a>

                                <button class="btn btn-success btn-sm add-to-cart-article-btn"
                                    data-id="{{ $product->id }}">
                                    <i class="bi bi-cart"></i> Adicionar ao carrinho
                                </button>

                            </div>
                        </div>

                    </div>
                </div>
            @endif

            <div class="modal fade" id="shareModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">Compartilhar Planta</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body text-center">

                            <h5>{{ $plant->popular_name }}</h5>
                            <p><em>{{ $plant->scientific_name }}</em></p>

                            <div class="my-3">
                                {!! QrCode::size(180)->generate(url("/plant/{$plant->id}/{$plant->slug}")) !!}
                            </div>

                            @php
                                $message =
                                    "*{$plant->popular_name}*\n" .
                                    "Nome científico: {$plant->scientific_name}\n\n" .
                                    "Acesse o artigo completo no link abaixo:\n" .
                                    url("/plant/{$plant->id}/{$plant->slug}");
                            @endphp

                            <textarea id="shareMessage" class="form-control" rows="4" hidden>{{ $message }}</textarea>


                        </div>

                        <div class="modal-footer d-flex justify-content-center gap-2">

                            <a id="whatsappLink" class="btn btn-success" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i> WhatsApp
                            </a>

                            <a id="facebookLink" class="btn btn-primary" target="_blank">
                                <i class="bi bi-facebook me-1"></i> Facebook
                            </a>

                            <button class="btn btn-secondary" onclick="copyShareLink()">
                                <i class="bi bi-link-45deg me-1"></i> Copiar Link
                            </button>

                        </div>

                    </div>
                </div>
            </div>

            {{-- Informações adicionais --}}
            <div class="extra-info">
                <h2>Informações Adicionais</h2>
                <p class="text-info-custom"><strong>Habitat:</strong> {{ $plant->habitat }}</p>
                <p class="text-info-custom"><strong>Partes Utilizadas:</strong></p>
                <ul class="items-list">
                    @foreach ($plant->useful_parts as $part)
                        <li>{{ $part }}</li>
                    @endforeach
                </ul>
                <p class="text-info-custom"><strong>Características:</strong> {{ $plant->characteristics }}</p>
                <p class="text-info-custom"><strong>Observações:</strong> {{ $plant->observations }}</p>
                <p class="text-info-custom"><strong>Composição Química:</strong> {{ $plant->chemical_composition }}</p>
                <p class="text-info-custom"><strong>Contraindicações:</strong> {{ $plant->contraindications }}</p>
                <p class="text-info-custom"><strong>Modo de Uso:</strong> {{ $plant->mode_of_use }}</p>
                <p class="text-info-custom"><strong>Referências:</strong> {{ $plant->info_references }}</p>
                <p class="text-info-custom"><strong>Tags:</strong>
                    @foreach ($plant->tags as $tag)
                        <span class="tag-tooltip">
                            <span class="badge bg-success">{{ $tag->name }}</span>
                            <span class="tooltip-box">
                                <strong>{{ $tag->name }}</strong><br>
                                {{ $tag->description }}
                            </span>
                        </span>
                    @endforeach
                </p>

            </div>
        </div>

        <div class="comments-section">
            <h3 class="mb-3">Comentários</h3>

            {{-- Formulário para comentar --}}
            <div class="card mb-4 p-3">

                @guest
                    <textarea class="form-control mb-2" rows="3" placeholder="Faça login para comentar..." disabled></textarea>

                    <a href="{{ route('login') }}" class="btn btn-primary w-100">
                        Entrar para comentar
                    </a>
                @else
                    {{-- Verificação de STRIKES --}}
                    @if (auth()->user()->comment_strikes >= 3)
                        <div class="alert alert-warning mb-0">
                            A função de comentários está temporariamente indisponível para você.
                        </div>
                    @else
                        <form action="{{ route('plant-comments.store', $plant->id) }}" method="POST">
                            @csrf
                            <textarea name="comment" class="form-control mb-2" rows="3" required placeholder="Escreva seu comentário..."></textarea>
                            <button class="btn btn-primary">Enviar Comentário</button>
                        </form>
                    @endif
                @endguest

            </div>

            {{-- Lista de comentários --}}
            @foreach ($plant->comments as $comment)
                <div class="comment-box d-flex p-3 mb-3 rounded shadow-sm">

                    {{-- Avatar --}}
                    <div class="comment-avatar me-3">
                        <div class="avatar-circle">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </div>
                    </div>

                    <div class="comment-content flex-grow-1">

                        {{-- Cabeçalho --}}
                        <div class="d-flex justify-content-between align-items-center mb-1">

                            {{-- Nome e data --}}
                            <div>
                                <strong>{{ $comment->user->name }}</strong>
                                <small class="text-muted d-block">
                                    {{ $comment->created_at->timezone('America/Sao_Paulo')->format('d/m/Y H:i') }}
                                </small>
                            </div>

                            {{-- Botão Denunciar — para qualquer usuário logado diferente do autor --}}
                            @if (Auth::check() && Auth::id() !== $comment->user_id)
                                <form action="{{ route('plant-comments.report', $comment->id) }}" method="POST"
                                    class="m-0 p-0" onsubmit="return confirm('Deseja denunciar este comentário?');">

                                    @csrf
                                    <button class="btn btn-sm btn-light border-0 d-flex align-items-center"
                                        style="font-size: 0.85rem; padding: 2px 6px;">
                                        <i class="bi bi-flag-fill text-danger me-1"></i>
                                        Denunciar
                                    </button>
                                </form>
                            @endif
                        </div>

                        <p class="mt-2">{{ $comment->comment }}</p>

                        {{-- Botões --}}
                        <div class="d-flex gap-2">

                            {{-- Editar — dono do comentário --}}
                            @if (Auth::check() && Auth::id() === $comment->user_id)
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editCommentModal{{ $comment->id }}">
                                    Editar
                                </button>
                            @endif

                            {{-- Excluir — dono do comentário --}}
                            @if (Auth::check() && Auth::id() === $comment->user_id)
                                <form action="{{ route('plant-comments.destroy', $comment->id) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza que deseja excluir este comentário?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        Excluir
                                    </button>
                                </form>
                            @endif

                            {{-- Moderação — ADMIN --}}
                            @if (Auth::check() && Auth::user()->user_lvl === 'admin')
                                <form action="{{ route('plant-comments.moderateDelete', $comment->id) }}" method="POST"
                                    onsubmit="return confirm('Tem certeza que deseja EXCLUIR este comentário para fins de moderação?\n\nEssa ação dará um STRIKE ao usuário autor do comentário. Ao atingir 3 strikes, ele será impedido de comentar até ser desbloqueado.');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-warning">
                                        Moderar (Excluir + Strike)
                                    </button>
                                </form>
                            @endif

                        </div>

                    </div>
                </div>

                {{-- Modal de edição --}}
                @if (Auth::check() && Auth::id() === $comment->user_id)
                    <div class="modal fade" id="editCommentModal{{ $comment->id }}">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Comentário</h5>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <form action="{{ route('plant-comments.update', $comment->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-body">
                                        <textarea name="comment" rows="4" class="form-control">{{ $comment->comment }}</textarea>
                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-primary">Salvar</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>


        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 99999">
            <div id="copyToast" class="toast align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        Link copiado para a área de transferência!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>


        {{-- Script para miniaturas --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const carouselId = '#plantCarousel{{ $plant->id }}';
                const thumbnails = document.querySelectorAll('[data-bs-target="' + carouselId + '"]');
                const carousel = document.querySelector(carouselId);

                carousel.addEventListener('slide.bs.carousel', function(event) {
                    thumbnails.forEach(thumb => thumb.classList.remove('active'));
                    thumbnails[event.to].classList.add('active');
                });
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const message = document.getElementById('shareMessage').value.trim();
                const encodedMessage = encodeURIComponent(message);
                const pageUrl = "{{ url("/plant/{$plant->id}/{$plant->slug}") }}";
                const encodedUrl = encodeURIComponent(pageUrl);

                // WhatsApp (mensagem completa)
                document.getElementById('whatsappLink').href =
                    `https://wa.me/?text=${encodedMessage}`;

                // Facebook
                document.getElementById('facebookLink').href =
                    `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
            });

            function showCopyToast() {
                const toastEl = document.getElementById("copyToast");
                const t = new bootstrap.Toast(toastEl);
                t.show();
            }


            // Copiar apenas o link
            function copyShareLink() {
                const pageUrl = "{{ url("/plant/{$plant->id}/{$plant->slug}") }}";

                navigator.clipboard.writeText(pageUrl).then(() => {
                    showCopyToast();
                });
            }
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const buttons = document.querySelectorAll('.add-to-cart-article-btn');

                buttons.forEach(button => {
                    button.addEventListener('click', function() {
                        const productId = this.dataset.id;

                        fetch(`/cart/add/${productId}`, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                // Notificação estilo index
                                const alertBox = document.createElement('div');
                                alertBox.className =
                                    'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                                alertBox.style.zIndex = '1050';
                                alertBox.innerHTML = `
                    <strong>✔</strong> ${data.message || 'Produto adicionado ao carrinho!'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                                document.body.appendChild(alertBox);

                                setTimeout(() => {
                                    alertBox.classList.remove('show');
                                    alertBox.addEventListener('transitionend', () =>
                                        alertBox.remove());
                                }, 3000);
                            })
                            .catch(error => console.error('Erro ao adicionar ao carrinho:', error));
                    });
                });
            });
        </script>


    @endsection
