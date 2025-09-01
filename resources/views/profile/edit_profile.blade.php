@extends('layouts.main')

@include('includes.toast')

@section('content')
<div class="container my-4">
    <h1 class="text-center mb-4">Editar Perfil</h1>

    <div class="card mx-auto shadow-sm" style="max-width: 450px;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informações do usuário</h5>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item"><strong>Nome:</strong> {{ $user->name }}</li>
                <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
                <li class="list-group-item"><strong>Nível de Usuário:</strong> {{ $levels[$user->user_lvl] ?? $user->user_lvl }}</li>
            </ul>

            <div class="d-grid">
                <!-- Botão para abrir o primeiro modal -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editNameModal">
                    Editar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1: Editar Nome -->
<div class="modal fade" id="editNameModal" tabindex="-1" aria-labelledby="editNameModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('profile.updateName') }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editNameModalLabel">Editar Nome</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" class="form-control" placeholder="********" readonly>
                </div>
                <div class="d-grid mt-2">
                    <!-- Botão para abrir o segundo modal -->
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal" data-bs-dismiss="modal">
                        Editar Senha
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

<!-- Modal 2: Alterar Senha -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('profile.updatePassword') }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Alterar Senha</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Senha Atual</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">Nova Senha</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="mb-3">
                    <label for="new_password_confirmation" class="form-label">Confirmar Nova Senha</label>
                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Senha</button>
            </div>
        </div>
    </form>
  </div>
</div>
@endsection
