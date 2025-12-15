{{-- PAINEL USUÁRIOS --}}
@include('includes.toast')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold ms-3 my-3">Usuários</h4>
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
                    <th style="width: 160px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $levels[$user->user_lvl] ?? $user->user_lvl }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary js-manage-user" data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                data-level="{{ $user->user_lvl }}" data-strikes="{{ $user->comment_strikes ?? 0 }}">
                                Gerenciar
                            </button>

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
                <div><strong>Nível:</strong> {{ $levels[$user->user_lvl] ?? $user->user_lvl }}</div>

                <button class="btn btn-primary btn-sm w-100 mt-3 js-manage-user" data-bs-toggle="modal"
                    data-bs-target="#manageUserModal" data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                    data-email="{{ $user->email }}" data-level="{{ $user->user_lvl }}"
                    data-strikes="{{ $user->comment_strikes ?? 0 }}">
                    Gerenciar
                </button>
            </div>
        @endforeach
    </div>

</div>
