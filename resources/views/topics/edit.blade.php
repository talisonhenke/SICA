@extends('layouts.main')

@section('content')
<style>
    .create-topic-container {
        max-width: 800px;
        margin: 3rem auto;
        background-color: var(--color-surface-secondary);
        padding: 2.5rem;
        border-radius: 1rem;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    .create-topic-title {
        text-align: center;
        color: var(--color-secondary);
        font-weight: 800;
        font-size: 2rem;
        margin-bottom: 2rem;
    }

    label {
        font-weight: 600;
        color: var(--color-primary-dark);
        margin-bottom: 0.4rem;
        display: block;
    }

    .form-control {
        border: 1px solid var(--color-muted);
        border-radius: 0.6rem;
        background-color: var(--color-input-bg)
        color: var(--color-input-text);
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--color-accent);
        box-shadow: 0 0 0 0.2rem rgba(108, 139, 88, 0.25);
        background-color: #fff;
        color: var(--color-text-dark);
    }

    /* Campos com erro */
    .is-invalid {
        border-color: var(--color-danger) !important;
        box-shadow: 0 0 0 0.2rem rgba(217, 83, 79, 0.25);
    }

    .invalid-feedback {
        color: var(--color-danger);
        font-size: 0.9rem;
        margin-top: 0.3rem;
        font-weight: 500;
    }

    textarea.form-control {
        resize: vertical;
    }

    .btn-submit {
        background-color: var(--color-accent);
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 0.6rem;
        padding: 0.75rem 1.5rem;
        transition: background 0.3s ease, transform 0.1s ease;
        width: 100%;
        margin-top: 1rem;
    }

    .btn-submit:hover {
        background-color: var(--color-secondary);
        transform: translateY(-2px);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    .file-label {
        display: block;
        background-color: var(--color-primary-light);
        color: #fff;
        padding: 0.6rem 1rem;
        border-radius: 0.5rem;
        text-align: center;
        cursor: pointer;
        transition: background 0.3s ease;
        font-weight: 600;
    }

    .file-label:hover {
        background-color: var(--color-primary);
    }

    input[type="file"] {
        display: none;
    }

    .preview-image {
        display: block;
        margin: 1rem auto;
        max-width: 100%;
        border-radius: 0.5rem;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
    }

    .text-muted {
        font-size: 0.85rem;
        color: var(--color-muted);
        text-align: center;
        display: block;
        margin-top: 0.5rem;
    }

    .mention-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--color-secondary);
    color: var(--color-surface-primary);
    padding: 3px 8px;
    border-radius: 12px;
    margin: 0 4px;
    font-weight: 600;
    font-size: .95rem;
}

.mention-chip .chip-remove {
    background: transparent;
    border: none;
    color: rgba(255,255,255,0.9);
    font-weight: 700;
    cursor: pointer;
    padding: 0 4px;
    line-height: 1;
}

#content-editor {
    min-height: 150px; /* altura aproximada de rows="5" */
    padding: 10px;
    border-radius: 6px;
    overflow-y: auto;
}

#content-editor:empty::before {
    content: attr(data-placeholder);
    color: #999;
    pointer-events: none;
}
</style>

<div class="create-topic-container">
    <h2 class="create-topic-title">📝 Editar Tópico</h2>

    <form action="{{ route('topics.update', $topic->id) }}" method="POST" enctype="multipart/form-data" onsubmit="serializeEditorContent()" novalidate>
        @csrf
        @method('PUT')

        <div class="form-group mb-4">
            <label for="title">Título</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror"
                   name="title" value="{{ old('title', $topic->title) }}" placeholder="Digite o título do tópico..." required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="description">Descrição</label>
            <textarea class="form-control @error('description') is-invalid @enderror"
                      name="description" rows="3" placeholder="Uma breve descrição sobre o conteúdo..." required>{{ old('description', $topic->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="content">Conteúdo</label>
            <input type="hidden" name="content" id="content-hidden">
            <div id="content-editor" name="content-editor" data-placeholder="Digite o conteúdo do tópico aqui..." class="form-control @error('content') is-invalid @enderror" contenteditable="true" rows="5" placeholder="Escreva aqui o conteúdo completo do tópico..." required>{{ old('content', $topic->content) }}</div>
            {{-- <textarea class="form-control @error('content') is-invalid @enderror"
                      name="content" rows="5" placeholder="Escreva aqui o conteúdo completo do tópico..." required>{{ old('content', $topic->content) }}</textarea> --}}

            @error('content')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4 text-center">
            <label for="image" class="file-label">📷 Alterar Imagem do Tópico</label>
            <input type="file" id="image" name="image" accept="image/*">

            @if($topic->image)
                <img id="preview" src="{{ asset($topic->image) }}" class="preview-image" alt="Imagem atual do tópico">
                <small class="text-muted">A imagem atual será substituída se uma nova for escolhida.</small>
            @else
                <img id="preview" class="preview-image d-none" alt="Prévia da imagem">
            @endif

            @error('image')
                <div class="invalid-feedback d-block text-center">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">💾 Atualizar Tópico</button>
        <a href="{{ route('topics.index') }}" class="btn-cancel">❌ Cancelar</a>
    </form>
</div>

<script>
    // Preview da nova imagem
    document.getElementById('image').addEventListener('change', function (event) {
        const [file] = event.target.files;
        const preview = document.getElementById('preview');
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        }
    });
</script>

<script>
const plants = @json(
    $plants->map(fn($p) => [
        'key' => $p->popular_name,
        'id'  => $p->id,
    ])
);
</script>


<!-- certifique-se de ter Tribute.js carregado -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editor = document.getElementById('content-editor');

    // seu array de plantas (ex.: [{key:"Malva", id:1}, ...])
    const plants = @json($plants->map(fn($p) => ['key'=>$p->popular_name,'id'=>$p->id]));

    function escapeHtml(text) {
        return (text + '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    const tribute = new Tribute({
        trigger: '@',
        values: plants,
        lookup: 'key',
        fillAttr: 'key',
        allowSpaces: true,
        menuItemTemplate: function (item) {
            // como aparecem as sugestões no menu
            return `<div class="mention-item">${escapeHtml(item.string)}</div>`;
        },
        selectTemplate: function (item) {
            if (!item || !item.original) return '';

            const id = item.original.id;
            const name = escapeHtml(item.original.key);

            // retornamos HTML (string) — Tribute injeta essa string no editor
            // contenteditable=false impede edição do chip
            // usamos &nbsp; para garantir um espaço protegido depois do chip
            return `<span class="mention-chip" contenteditable="false" data-plant-id="${id}">
                        <span class="mention-name">${name}</span>
                        <button type="button" class="chip-remove" contenteditable="false" aria-label="remover">×</button>
                    </span>&nbsp;`;
        },
        replaceTextSuffix: '' // já usamos &nbsp; no template acima
    });

    tribute.attach(editor);

    // Delegação: remover chip ao clicar no botão × (funciona com HTML inserido)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.chip-remove');
        if (!btn) return;
        const chip = btn.closest('.mention-chip');
        if (!chip) return;

        chip.remove();
        // reposiciona caret no final
        placeCaretAtEnd(editor);
        editor.focus();
    });

    function placeCaretAtEnd(el) {
        el.focus();
        if (typeof window.getSelection !== "undefined" && typeof document.createRange !== "undefined") {
            const range = document.createRange();
            range.selectNodeContents(el);
            range.collapse(false);
            const sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
        }
    }
});
</script>

<script>
    function editorToTextWithTokens() {
    const clone = editor.cloneNode(true);
    clone.querySelectorAll('.mention-chip').forEach(chip => {
        const id = chip.dataset.plantId;
        const name = chip.querySelector('.mention-name')?.textContent ?? '';
        const token = `[[plant:${id}:${name}]]`;
        const textNode = document.createTextNode(token);
        chip.replaceWith(textNode);
    });
    return clone.textContent;
}

// no submit do form:
form.addEventListener('submit', function(e) {
    const hidden = document.getElementById('content_hidden');
    hidden.value = editorToTextWithTokens();
});

</script>


<script>
function serializeEditorContent() {
    const editor = document.getElementById("content-editor");
    let output = "";

    editor.childNodes.forEach(node => {

        // Caso seja um chip
        if (node.nodeType === Node.ELEMENT_NODE && node.classList.contains("mention-chip")) {
            const plantId = node.dataset.plantId;
            output += `[[planta:${plantId}]]`;
        }

        // Caso seja texto puro
        else if (node.nodeType === Node.TEXT_NODE) {
            output += node.textContent;
        }

        // Caso seja uma quebra de linha <br>
        else if (node.nodeName === "BR") {
            output += "\n";
        }
    });

    document.getElementById("content-hidden").value = output.trim();
}
</script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
    const editor = document.getElementById("content-editor");
    const raw = @json(old('content', $topic->content));

    // Converter tokens em chips
    const html = raw.replace(/\[\[planta:(\d+)\]\]/g, (match, id) => {
        return `
            <span class="mention-chip" contenteditable="false" data-plant-id="${id}">
                <span class="mention-name">Carregando...</span>
                <button type="button" class="chip-remove">×</button>
            </span>&nbsp;
        `;
    });

    editor.innerHTML = html;

    // Agora preencher os nomes das plantas (opcional, mas elegante)
    const plants = @json(
        $plants->map(fn($p) => ['id' => $p->id, 'name' => $p->popular_name])
    );

    editor.querySelectorAll(".mention-chip").forEach(chip => {
        const id = Number(chip.dataset.plantId);
        const p = plants.find(pl => pl.id === id);

        if (p) {
            chip.querySelector(".mention-name").textContent = p.name;
        }
    });
});

</script>

@endsection
