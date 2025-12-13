<div class="dashboard-sidebar">

    <h5 class="mb-4">Painel Administrativo</h5>

    <button class="dashboard-link active" data-panel="orders">
        Pedidos
        <span class="badge bg-warning">
            {{ $orderStats['pending'] }}
        </span>
    </button>

    <button class="dashboard-link" data-panel="moderation">
        Moderação
        <span class="badge bg-danger">
            {{ $moderationCount }}
        </span>
    </button>

    <button class="dashboard-link" data-panel="tags">
        Tags
        <span class="badge bg-secondary">
            {{ $tagsCount }}
        </span>
    </button>

    <button class="dashboard-link" data-panel="users">
        Usuários
        <span class="badge bg-primary">
            {{ $usersCount }}
        </span>
    </button>

</div>
