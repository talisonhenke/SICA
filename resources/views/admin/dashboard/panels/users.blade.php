{{-- PAINEL: USUÁRIOS --}}

@include('includes.toast')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Usuários</h4>
</div>

<div class="card p-3">

    {{-- DESKTOP --}}
    <div class="d-none d-md-block">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Nível</th>
                    <th style="width: 220px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            {{ $levels[$user->user_lvl] ?? $user->user_lvl }}
                        </td>
                        <td>
                            <select class="form-select form-select-sm js-user-level"
                                data-url="{{ route('admin.dashboard.panels.users.updateLevel', $user->id) }}"
                                data-username="{{ $user->name }}" data-previous-value="{{ $user->user_lvl }}">
                                <option value="user" {{ $user->user_lvl === 'user' ? 'selected' : '' }}>
                                    Usuário
                                </option>
                                <option value="admin" {{ $user->user_lvl === 'admin' ? 'selected' : '' }}>
                                    Administrador
                                </option>
                            </select>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MOBILE --}}
    <div class="d-md-none">
        @foreach ($users as $user)
            <div class="border rounded p-3 mb-3 bg-light">
                <div><strong>ID:</strong> {{ $user->id }}</div>
                <div><strong>Nome:</strong> {{ $user->name }}</div>
                <div><strong>Email:</strong> {{ $user->email }}</div>

                {{-- TODO: trocar ações por botão e modal  --}}
                <div class="mt-3">
                    <label class="form-label fw-bold">Nível</label>
                    <select class="form-select form-select-sm js-user-level"
                        data-url="{{ route('admin.dashboard.panels.users.updateLevel', $user->id) }}"
                        data-username="{{ $user->name }}" data-previous-value="{{ $user->user_lvl }}">

                        @foreach ($levels as $value => $label)
                            <option value="{{ $value }}" @selected($user->user_lvl === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endforeach
    </div>

</div>
