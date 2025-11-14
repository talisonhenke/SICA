@extends('layouts.main')

@section('content')
<div class="container my-4 users-container">

    <h1 class="text-center mb-4 users-title">Lista de Usuários</h1>

    @if(Auth::check() && Auth::user()->user_lvl === 'admin')

        {{-- TABELA DESKTOP --}}
        <table class="table table-bordered table-custom d-none d-md-table">
            <thead>
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
                        {{-- Alterar nível --}}
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

                        {{-- Excluir --}}
                        <form action="#" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-custom-delete">Excluir</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- TABELA MOBILE --}}
        <div class="d-block d-md-none">
            @foreach($users as $user)
            <table class="table table-bordered mb-4 table-mobile">
                <tbody>
                    <tr>
                        <td>ID</td>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <td>Nome</td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td>Nível de Usuário</td>
                        <td>{{ $levels[$user->user_lvl] ?? $user->user_lvl }}</td>
                    </tr>
                    <tr>
                        <td>Ações</td>
                        <td class="d-flex gap-2">
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

                            <form action="#" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-custom-delete">Excluir</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
            @endforeach
        </div>

    @else
        <div class="alert alert-custom text-center">
            Você não tem permissão para acessar esta página.
        </div>
    @endif

</div>
@endsection
