<h4>Tags Recentes</h4>

<ul class="list-group mb-3">
    @foreach($recentTags as $tag)
        <li class="list-group-item d-flex justify-content-between">
            {{ $tag->name }}
            <small>{{ $tag->created_at->format('d/m/Y') }}</small>
        </li>
    @endforeach
</ul>

<a href="{{ route('tags.index') }}" class="btn btn-sm btn-outline-secondary">
    Gerenciar tags
</a>
