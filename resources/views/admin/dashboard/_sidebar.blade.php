<style>
    .dashboard-link {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Badge de alerta */
    .badge-alert {
        background: var(--color-danger);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
    }
</style>

<div class="dashboard-sidebar">

    <h5 class="mb-4">Painel Administrativo</h5>

    <button class="dashboard-link" data-panel="orders">
        Pedidos

        @if ($orderStats['pending'] > 0)
            <span class="badge badge-alert">
                {{ $orderStats['pending'] }}
            </span>
        @endif
    </button>

    <button class="dashboard-link" data-panel="moderation">
        Moderação

        @if ($moderationCount > 0)
            <span class="badge badge-alert">
                {{ $moderationCount }}
            </span>
        @endif
    </button>

    <button class="dashboard-link" data-panel="tags">
        Tags
    </button>

    <button class="dashboard-link" data-panel="users">
        Usuários
    </button>

    <button class="dashboard-link" data-panel="reviews">
        Avaliações

        @if ($newReviewsCount > 0)
            <span class="badge badge-alert">
                {{ $newReviewsCount }}
            </span>
        @endif
    </button>

</div>
