<div class="mb-3 orders-filters">
    <select id="filterSelect" class="form-select form-select-sm" style="max-width: 260px;">
        <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>Todos</option>
        <option value="reported" {{ $filter === 'reported' ? 'selected' : '' }}>Reportados</option>
        <option value="suspect" {{ $filter === 'suspect' ? 'selected' : '' }}>Suspeitos ≥ 0.5</option>
        <option value="high" {{ $filter === 'high' ? 'selected' : '' }}>Graves ≥ 0.7</option>
    </select>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Usuário</th>
                        <th>Comentário</th>
                        <th>Toxicidade</th>
                        <th>Reportado</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($comments as $comment)
                        <tr>
                            <td>#{{ $comment->id }}</td>
                            <td>{{ $comment->user->name }}</td>
                            <td>{{ Str::limit($comment->comment, 70) }}</td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    {{ number_format($comment->toxicity_level, 2) }}
                                </span>
                            </td>
                            <td>
                                @if ($comment->reported)
                                    <span class="badge bg-danger">Sim</span>
                                @else
                                    <span class="badge bg-success">Não</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#moderateModal{{ $comment->id }}">
                                    Moderar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Nenhum comentário aguardando moderação.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- {{ $comments->links() }} --}}

@foreach ($comments as $comment)
    @php
        $baseRoute = $comment->comment_type === 'topic' ? 'topic-comments' : 'plant-comments';
    @endphp

    <div class="modal fade" id="moderateModal{{ $comment->id }}">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        Moderar Comentário #{{ $comment->id }}
                    </h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-1"><strong>Usuário:</strong> {{ $comment->user->name }}</p>
                    <p class="mb-1"><strong>Toxicidade:</strong> {{ $comment->toxicity_level }}</p>
                    <p class="mb-3">
                        <strong>Reportado:</strong>
                        {{ $comment->reported ? 'Sim' : 'Não' }}
                    </p>

                    <div class="border rounded p-3 bg-light">
                        {{ $comment->comment }}
                    </div>
                </div>

                <div class="modal-footer d-flex flex-wrap gap-2 justify-content-between">

                    <button type="button" class="btn btn-danger js-moderate-delete"
                        data-url="{{ route($baseRoute . '.ajax.moderateDelete', $comment->id) }}">
                        Excluir + Strike
                    </button>


                    <button type="button" class="btn btn-success js-allow-comment"
                        data-url="{{ route($baseRoute . '.ajax.allow', $comment->id) }}">
                        Permitir
                    </button>


                    <button type="button" class="btn btn-dark js-block-user"
                        data-url="{{ route($baseRoute . '.ajax.blockUser', $comment->user->id) }}">
                        Bloquear Usuário
                    </button>


                </div>

            </div>
        </div>
    </div>
@endforeach

<script>
    document.querySelectorAll('.js-moderate-delete').forEach(button => {
        button.addEventListener('click', async () => {

            if (!confirm('Excluir e dar STRIKE ao usuário?')) return;

            const response = await fetch(button.dataset.url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                // aqui depois você coloca toast
                window.location.reload();
            } else {
                alert(data.message);
            }
        });
    });
</script>

<script>
    /* ============================
   PERMITIR COMENTÁRIO
============================ */
    document.querySelectorAll('.js-allow-comment').forEach(button => {
        button.addEventListener('click', async () => {

            if (!confirm('Deseja permitir este comentário?')) return;

            const response = await fetch(button.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Erro ao permitir comentário.');
            }
        });
    });


    /* ============================
       BLOQUEAR USUÁRIO
    ============================ */
    document.querySelectorAll('.js-block-user').forEach(button => {
        button.addEventListener('click', async () => {

            if (!confirm('Bloquear este usuário definitivamente para comentários?')) return;

            const response = await fetch(button.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Erro ao bloquear usuário.');
            }
        });
    });
</script>
<script>
document.getElementById('filterSelect').addEventListener('change', async function () {

    const filter = this.value;

    const response = await fetch(
        "{{ route('admin.dashboard.moderation.ajax') }}?filter=" + filter,
        { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
    );

    const html = await response.text();

    document.getElementById('moderation-panel-content').innerHTML = html;
});
</script>
