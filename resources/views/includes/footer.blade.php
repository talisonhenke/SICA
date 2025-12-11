<div class="row align-items-center d-block">

    {{-- SOCIAL ICONS (comentado, manter assim) --}}
    {{-- 
    <div class="col-12">
      <ul class="nav justify-content-center list-unstyled">
          <li class="mx-2"><a class="text-body-secondary" href="#"><i class="bi bi-whatsapp social-icons-style"></i></a></li>
          <li class="mx-2"><a class="text-body-secondary" href="#"><i class="bi bi-instagram social-icons-style"></i></a></li>
          <li class="mx-2"><a class="text-body-secondary" href="#"><i class="bi bi-linkedin social-icons-style"></i></a></li>
          <li class="mx-2"><a class="text-body-secondary" href="#"><i class="bi bi-github social-icons-style"></i></a></li>
          <li class="mx-2"><a class="text-body-secondary" href="#"><i class="bi bi-facebook social-icons-style"></i></a></li>
      </ul>
    </div>
    --}}

    @auth
    @if (!auth()->user()->isAdmin())
        @php
            $myReview = App\Models\SiteReview::where('user_id', auth()->id())->first();
        @endphp

        <div class="col-12 text-center mb-3 mt-2">
            <h6 class="text-white mb-2">Nos dê sua opinião</h6>

            {{-- SE JÁ AVALIOU --}}
            @if ($myReview)
                <div>
                    <div class="mb-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $myReview->rating ? '-fill text-warning' : ' text-secondary' }}"></i>
                        @endfor
                    </div>

                    <p class="text-white-50">Obrigado por sua avaliação!</p>

                    <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#updateReviewModal">
                        Alterar avaliação
                    </button>
                </div>
            @else
                {{-- SE AINDA NÃO AVALIOU --}}
                <form action="{{ route('review.store') }}" method="POST" class="d-inline-block">
                    @csrf

                    <div class="star-rating mb-2">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" required>
                            <label for="star{{ $i }}"><i class="bi bi-star-fill"></i></label>
                        @endfor
                    </div>

                    <textarea name="comment" class="form-control bg-dark text-white mb-2" placeholder="Comentário (opcional)" rows="2"></textarea>

                    <button class="btn btn-success btn-sm">Enviar</button>
                </form>
            @endif
        </div>

        {{-- MODAL ATUALIZAR AVALIAÇÃO --}}
        @if ($myReview)
            <div class="modal fade" id="updateReviewModal">
                <div class="modal-dialog">
                    <form action="{{ route('review.update') }}" method="POST" class="modal-content bg-dark text-white">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">Alterar Avaliação</h5>
                            <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <label>Nova nota:</label>
                            <div class="star-rating mb-3">
                                @for ($i = 1; $i <= 5; $i++)
                                    <input type="radio" name="rating" id="staru{{ $i }}" value="{{ $i }}"
                                        {{ $myReview->rating == $i ? 'checked' : '' }}>
                                    <label for="staru{{ $i }}"><i class="bi bi-star-fill"></i></label>
                                @endfor
                            </div>

                            <label>Comentário (opcional):</label>
                            <textarea name="comment" class="form-control bg-dark text-white" rows="3">{{ $myReview->comment }}</textarea>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    @endif
@endauth


    <div class="col-12 text-center mt-2">
        <span class="text-white">&copy; 2025 S.I.C.A</span>
    </div>
</div>

<style>
    .star-rating {
        display: inline-flex;
        flex-direction: row-reverse;
        /* ESSENCIAL */
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        cursor: pointer;
        font-size: 1.6rem;
        color: #666;
        transition: color 0.2s;
    }

    /* HOVER – acende estrela atual e todas as anteriores */
    .star-rating label:hover,
    .star-rating label:hover~label {
        color: #ffc107;
    }

    /* ESTRELAS SELECIONADAS */
    .star-rating input:checked~label {
        color: #ffc107;
    }
</style>
