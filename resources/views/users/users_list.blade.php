@extends('layouts.main')

@section('content')
<div class="container my-4">
    <h1 class="text-center mb-4">Lista de Usuários</h1>

    @if(Auth::check() && Auth::user()->user_lvl === 'admin')
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Nível de Usuário</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $levels[$user->user_lvl] ?? $user->user_lvl }}</td>
                        <td class="d-flex gap-2">
                            {{-- Alterar nível de acesso --}}
                            {{-- <form action="{{ route('users.update', $user->id) }}" method="POST" class="user-level-form"> --}}
                            <form action="{{ route('users.updateLevel', $user->id) }}" method="POST" class="user-level-form">
                                @csrf
                                @method('PATCH')
                                <select name="user_lvl" data-username="{{ $user->name }}" class="form-select form-select-sm">
                                    @foreach(['member', 'moderator', 'admin'] as $level)
                                        <option value="{{ $level }}" {{ $user->user_lvl == $level ? 'selected' : '' }}>
                                            {{ $levels[$level] }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                            {{-- Excluir usuário --}}
                            {{-- <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');"> --}}
                            <form action="#" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-danger text-center">
            Você não tem permissão para acessar esta página.
        </div>
    @endif
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Array de traduções em JS
    const levels = @json($levels);

    document.querySelectorAll('.user-level-form select').forEach(select => {
        let previousValue = select.value; // valor inicial

        select.addEventListener('change', function() {
            const userName = select.getAttribute('data-username');
            const newValue = select.value;

            // traduz o valor do select
            const translatedValue = levels[newValue] ?? newValue;

            const confirmed = confirm(`Deseja mudar o cargo do usuário ${userName} para ${translatedValue}?`);

            if (confirmed) {
                previousValue = newValue; // atualiza valor anterior
                select.form.submit();
            } else {
                select.value = previousValue; // restaura valor antigo
            }
        });
    });
});
</script>