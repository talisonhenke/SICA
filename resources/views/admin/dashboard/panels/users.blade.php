<h4>Usuários Recentes</h4>

<ul class="list-group mb-3">
    @foreach($recentUsers as $user)
        <li class="list-group-item d-flex justify-content-between">
            {{ $user->name }}
            <small>{{ $user->created_at->format('d/m/Y') }}</small>
        </li>
    @endforeach
</ul>

{{-- <a href="{{ route('admin.ajax.users.index') }}" class="btn btn-sm btn-outline-primary">
    Gerenciar usuários
</a> --}}
