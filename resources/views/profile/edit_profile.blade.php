@extends('layouts.main')

@include('includes.toast')

@section('content')

<style>
    :root {
        --primary-color: #4CAF50;
        --primary-dark: #2E7D32;
        --accent-color: #FFC107;
        --danger-color: #dc3545;
        --bg-light: #f5f7fa;
        --bg-card: #ffffff;
        --text-dark: #2f2f2f;
        --text-muted: #6c757d;
        --border-color: #e0e0e0;
    }

    body {
        background: var(--bg-light);
    }

    .profile-wrapper {
        max-width: 850px;
        margin: auto;
    }

    .profile-header-card {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .profile-photo {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: var(--primary-color);
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 36px;
        font-weight: 700;
        margin-right: 20px;
    }

    .card-custom {
        background: var(--bg-card);
        border-radius: 14px;
        padding: 1.5rem;
        margin-top: 2rem;
        box-shadow: 0 4px 18px rgba(0,0,0,0.06);
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 1.2rem;
    }

    /* ENDEREÇOS */
    .address-entry {
        border-left: 4px solid var(--primary-color);
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 10px;
        background: var(--bg-light);
        transition: 0.2s;
    }

    .primary-card {
        background: #e8f5e9;
        box-shadow: 0 3px 10px rgba(0,0,0,0.07);
    }
</style>

<div class="container py-4">
    <div class="profile-wrapper">

        <!-- HEADER -->
        <div class="profile-header-card d-flex align-items-center">

            <div class="profile-photo">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            <div class="profile-info">
                <h3>{{ $user->name }}</h3>
                <p class="text-muted mb-1">{{ $user->email }}</p>

                <span class="badge-level">
                    {{ $levels[$user->user_lvl] ?? $user->user_lvl }}
                </span>
            </div>

            <div class="ms-auto">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editNameModal">
                    Editar Perfil
                </button>
            </div>
        </div>

        <!-- INFORMAÇÕES -->
        <div class="card-custom">
            <div class="section-title">Informações da Conta</div>

            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Nome:</strong> {{ $user->name }}</li>
                <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
                <li class="list-group-item"><strong>Nível:</strong> {{ $levels[$user->user_lvl] ?? $user->user_lvl }}</li>
            </ul>
        </div>

        <!-- ENDEREÇOS -->
        <div class="card-custom">
            <div class="section-title text-center">Meus Endereços</div>

            <div id="address-list">
                @forelse($addresses as $address)

                <div class="address-entry {{ $address->is_primary ? 'primary-card' : '' }}" data-id="{{ $address->id }}">

                    <div class="d-flex justify-content-between align-items-start">

                        <div>
                            <strong>{{ $address->street }}, Nº {{ $address->number }}</strong><br>
                            {{ $address->city }} - {{ $address->state }}<br>
                            CEP: {{ $address->zip_code }}
                        </div>

                        <div class="text-end">
                            <label class="form-check form-switch m-0">
                                <input 
                                    type="checkbox" 
                                    class="form-check-input primary-toggle"
                                    onclick="setPrimaryToggle(event, {{ $address->id }})"
                                    {{ $address->is_primary ? 'checked' : '' }}>
                                <small class="text-muted ms-1">Principal</small>
                            </label>
                        </div>

                    </div>

                    <div class="mt-2">
                        <button 
                            type="button" 
                            class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#editAddressModal"
                            onclick='openEditModal(@json($address))'>
                            Editar
                        </button>

                        <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button 
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Tem certeza que deseja excluir este endereço?');">
                                Excluir
                            </button>
                        </form>
                    </div>

                </div>

                @empty
                    <p class="text-muted text-center">Nenhum endereço cadastrado.</p>
                @endforelse
            </div>

            <div class="d-grid mt-3">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#googleAddressModal">
                    + Adicionar Endereço
                </button>
            </div>
        </div>

        @include('includes.google_address_modal')
        @include('includes.google_address_edit_modal')

    </div>
</div>

<script>
/* 
 * Define como endereço principal 
 */
async function setPrimaryToggle(event, id) {
    event.preventDefault();

    const el = event.target;
    const toggles = [...document.querySelectorAll('.primary-toggle')];
    const prev = toggles.map(t => t.checked);

    toggles.forEach(t => t.disabled = true);

    try {
        const resp = await fetch(`/addresses/${id}/primary`, {
            method: "PATCH",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({})
        });

        if (!resp.ok) throw new Error("Erro HTTP " + resp.status);

        const data = await resp.json();

        // Atualiza UI
        toggles.forEach(t => t.checked = false);
        el.checked = true;

        if (data.status === "ok") {
            showSessionToast(data.message);
            reorderPrimaryCard(el);
        }


    } catch (err) {
        console.error(err);
        toggles.forEach((t, i) => t.checked = prev[i]);
        alert("Não foi possível definir como principal.");
    } finally {
        toggles.forEach(t => t.disabled = false);
    }
}

/* 
 * Reordenar o card no topo
 */
function reorderPrimaryCard(el) {
    const card = el.closest(".address-entry");
    const container = document.querySelector("#address-list");

    // Remove classe dos outros cards
    document.querySelectorAll('.address-entry').forEach(c =>
        c.classList.remove('primary-card')
    );

    // Marca o atual
    card.classList.add('primary-card');

    // Move para o topo
    container.prepend(card);
}
</script>

@endsection
