@extends('layouts.main')

@include('includes.toast')

@section('content')

<style>
    /* --- PALETA DE CORES GLOBAL --- */
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

    /* ------------------------------ */
    /* CARD PRINCIPAL DE PERFIL       */
    /* ------------------------------ */

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

    .profile-info h3 {
        margin-bottom: 5px;
        font-weight: 600;
    }

    .badge-level {
        background: var(--primary-color);
        padding: 4px 10px;
        border-radius: 12px;
        color: white;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* ------------------------------ */
    /* CARDS SECUNDÁRIOS              */
    /* ------------------------------ */

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

    .info-item strong {
        color: var(--text-dark);
    }

    /* ------------------------------ */
    /* ENDEREÇOS                      */
    /* ------------------------------ */

    .address-card {
        border-left: 4px solid var(--primary-color);
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 8px;
        background: var(--bg-light);
    }

    .address-actions button {
        margin-right: 8px;
    }

    .icon-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
</style>

<div class="container py-4">
    <div class="profile-wrapper">

        <!-- ---------------------- -->
        <!-- CABEÇALHO DE PERFIL    -->
        <!-- ---------------------- -->
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

        <!-- ---------------------- -->
        <!-- INFORMAÇÕES DO USUÁRIO -->
        <!-- ---------------------- -->
        <div class="card-custom">
            <div class="section-title">Informações da Conta</div>

            <ul class="list-group list-group-flush">
                <li class="list-group-item info-item">
                    <strong>Nome:</strong> {{ $user->name }}
                </li>
                <li class="list-group-item info-item">
                    <strong>Email:</strong> {{ $user->email }}
                </li>
                <li class="list-group-item info-item">
                    <strong>Nível:</strong> {{ $levels[$user->user_lvl] ?? $user->user_lvl }}
                </li>
            </ul>
        </div>

        <!-- ---------------------- -->
        <!-- LISTA DE ENDEREÇOS     -->
        <!-- ---------------------- -->
        <div class="card-custom">
            <div class="section-title text-center">Meus Endereços</div>

            @forelse($addresses as $address)

                <div class="address-card">
                    <strong>{{ $address->street }}, Nº {{ $address->number }}</strong><br>
                    {{ $address->city }} - {{ $address->state }}<br>
                    CEP: {{ $address->zip_code }}

                    <div class="address-actions mt-2">

                        <!-- EDITAR -->
                        <button 
                            class="btn btn-outline-primary btn-sm icon-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#editAddressModal"
                            onclick='openEditModal(@json($address))'
                        >
                            ✏ Editar
                        </button>

                        <!-- EXCLUIR -->
                        <form action="{{ route('addresses.destroy', $address->id) }}" 
                              method="POST" 
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button 
                                class="btn btn-danger btn-sm icon-btn"
                                onclick="return confirm('Tem certeza que deseja excluir este endereço?');"
                            >
                                🗑 Excluir
                            </button>
                        </form>

                    </div>
                </div>

            @empty
                <p class="text-muted text-center">Nenhum endereço cadastrado.</p>
            @endforelse

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

@endsection
