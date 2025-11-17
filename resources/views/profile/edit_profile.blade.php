@extends('layouts.main')

@include('includes.toast')

@section('content')

<style>
    /* --- ESTILIZAÇÃO USANDO AS CORES GLOBAIS --- */
    :root {
        --primary-color: #4CAF50;     /* Verde padrão */
        --secondary-color: #2E7D32;   /* Verde escuro */
        --accent-color: #FFC107;      /* Amarelo */
        --danger-color: #dc3545;      /* Vermelho padrão */
        --light-color: #f8f9fa;
        --dark-color: #343a40;
    }

    .profile-card {
        max-width: 500px;
        margin: auto;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .profile-header {
        background: var(--primary-color);
        color: white;
    }

    .modal-header {
        background: var(--primary-color);
        color: white;
    }

    .address-box {
        padding: 1rem;
        border-radius: 10px;
        background: var(--light-color);
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .address-entry {
        border-left: 4px solid var(--primary-color);
        padding-left: 10px;
        margin-bottom: 10px;
    }

    .add-address-btn {
        background: var(--primary-color);
        border: none;
        color: white;
        font-weight: 600;
    }

    .add-address-btn:hover {
        background: var(--secondary-color);
        color: white;
    }

</style>

<div class="container my-4">

    <h1 class="text-center mb-4">Editar Perfil</h1>

    <!-- CARD DO PERFIL -->
    <div class="card profile-card shadow-sm">

        <div class="card-header profile-header">
            <h5 class="mb-0">Informações do Usuário</h5>
        </div>

        <div class="card-body">
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item"><strong>Nome:</strong> {{ $user->name }}</li>
                <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
                <li class="list-group-item"><strong>Nível de Usuário:</strong> {{ $levels[$user->user_lvl] ?? $user->user_lvl }}</li>
            </ul>

            <div class="d-grid">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editNameModal">
                    Editar
                </button>
            </div>
        </div>
    </div>

    <!-- SEÇÃO DE ENDEREÇOS -->
    <div class="mt-5">
        <h3 class="text-center mb-3">Meus Endereços</h3>

        <div class="address-box">

            <!-- Lista de endereços -->
            @forelse($addresses as $address)
                <div class="address-entry">
                    <strong>{{ $address->street }}, {{ $address->number }}</strong><br>
                    {{ $address->city }} - {{ $address->state }}<br>
                    CEP: {{ $address->zip_code }}
                    <div class="mt-2">
                        <a href="#" class="btn btn-sm btn-outline-primary">Editar</a>
                        <a href="#" class="btn btn-sm btn-outline-danger">Excluir</a>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center">Nenhum endereço cadastrado.</p>
            @endforelse

            <!-- MODAL GOOGLE ADDRESS -->
            @include('includes.google_address_modal')

            <div class="d-grid mt-3">
                <button class="btn add-address-btn" data-bs-toggle="modal" data-bs-target="#googleAddressModal">
                    + Adicionar Endereço
                </button>
            </div>

        </div>
    </div>

</div>





<!-- --------------------------- -->
<!-- MODAL: EDITAR NOME -->
<!-- --------------------------- -->
<div class="modal fade" id="editNameModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('profile.updateName') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar Nome</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>

                <div class="d-grid">
                    <button type="button" class="btn btn-secondary"
                        data-bs-toggle="modal" data-bs-target="#changePasswordModal"
                        data-bs-dismiss="modal">
                        Alterar Senha
                    </button>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Nome</button>
            </div>

        </div>
    </form>
  </div>
</div>





<!-- --------------------------- -->
<!-- MODAL: ALTERAR SENHA -->
<!-- --------------------------- -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('profile.updatePassword') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Alterar Senha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Senha Atual</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nova Senha</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirmar Nova Senha</label>
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>

        </div>
    </form>
  </div>
</div>


@endsection