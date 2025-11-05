<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TopicController extends Controller
{
    // Lista todos os tópicos
    public function index()
    {
        $topics = Topic::latest()->paginate(10);
        return view('topics.index', compact('topics'));
    }

    // Mostra o formulário de criação
    public function create()
    {
        return view('topics.create');
    }

    // Armazena um novo tópico
 public function store(Request $request)
{
    $request->validate([
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'content' => 'required|string',
    'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
], [
    'title.required' => 'O campo título é obrigatório.',
    'description.required' => 'A descrição é obrigatória.',
    'content.required' => 'O conteúdo precisa ser preenchido.',
    'image.required' => 'Selecione uma imagem para o tópico.',
    'image.image' => 'O arquivo selecionado deve ser uma imagem.',
    'image.mimes' => 'Formatos aceitos: JPG, JPEG, PNG e WEBP.',
    'image.max' => 'A imagem não pode ser maior que 5MB.',
]);


    DB::beginTransaction(); // inicia a transação

    try {
        // 1️⃣ Cria o tópico sem imagem inicialmente
        $topic = Topic::create([
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'image' => '',
            'featured' => false,
        ]);

        // 2️⃣ Define o caminho da pasta
        $topicDir = public_path("images/topics/{$topic->id}");

        // Cria a pasta se não existir
        if (!File::exists($topicDir)) {
            File::makeDirectory($topicDir, 0755, true);
        }

        // 3️⃣ Tenta mover a imagem
        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();

        // Move o arquivo (pode falhar se não tiver permissão)
        $image->move($topicDir, $imageName);

        // 4️⃣ Atualiza o caminho no banco
        $topic->image = "images/topics/{$topic->id}/{$imageName}";
        $topic->save();

        // 5️⃣ Tudo certo, confirma a transação
        DB::commit();

        return redirect()
            ->route('topics.index')
            ->with('msg', 'Tópico criado com sucesso!');

    } catch (\Exception $e) {
        // 6️⃣ Se deu erro em qualquer etapa, faz rollback
        DB::rollBack();

        // Remove registro "fantasma" se existir
        if (isset($topic) && $topic->exists) {
            $topic->delete();
        }

        // Remove pasta criada (se existir)
        if (isset($topicDir) && File::exists($topicDir)) {
            File::deleteDirectory($topicDir);
        }

        // Log para debug
        Log::error('Erro ao criar tópico: ' . $e->getMessage());

        // Retorna ao formulário com erro amigável
        return back()
            ->withInput()
            ->withErrors(['image' => 'Falha ao salvar o tópico. Tente novamente.']);
    }
}


    // Exibe um único tópico
    public function show(Topic $topic)
    {
        return view('topics.show', compact('topic'));
    }

    // Formulário de edição
    public function edit(Topic $topic)
    {
        return view('topics.edit', compact('topic'));
    }

    public function update(Request $request, $id)
{
    $topic = Topic::findOrFail($id);

     $request->validate([
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'content' => 'required|string',
    'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
], [
    'title.required' => 'O campo título é obrigatório.',
    'description.required' => 'A descrição é obrigatória.',
    'content.required' => 'O conteúdo precisa ser preenchido.',
    'image.required' => 'Selecione uma imagem para o tópico.',
    'image.image' => 'O arquivo selecionado deve ser uma imagem.',
    'image.mimes' => 'Formatos aceitos: JPG, JPEG, PNG e WEBP.',
    'image.max' => 'A imagem não pode ser maior que 5MB.',
]);

    DB::beginTransaction();

    try {
        // Atualiza os dados de texto
        $topic->title = $request->title;
        $topic->description = $request->description;
        $topic->content = $request->content;

        // Caminho base
        $topicDir = public_path("images/topics/{$topic->id}");

        // Se o usuário enviou uma nova imagem
        if ($request->hasFile('image')) {

            // Cria a pasta se não existir
            if (!File::exists($topicDir)) {
                File::makeDirectory($topicDir, 0755, true);
            }

            // Remove imagens antigas
            $oldFiles = glob($topicDir . '/*');
            foreach ($oldFiles as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            // Faz upload da nova imagem
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Move o arquivo (essa parte pode falhar)
            $image->move($topicDir, $imageName);

            // Atualiza o caminho no banco
            $topic->image = "images/topics/{$topic->id}/{$imageName}";
        }

        // Salva tudo
        $topic->save();
        DB::commit();

        return redirect()
            ->route('topics.index')
            ->with('msg', 'Tópico atualizado com sucesso!');

    } catch (\Exception $e) {
        DB::rollBack();

        // Caso a imagem tenha sido enviada parcialmente, remove a pasta
        if (isset($topicDir) && File::exists($topicDir)) {
            File::deleteDirectory($topicDir);
        }

        // Log para depuração
        Log::error('Erro ao atualizar tópico: ' . $e->getMessage());

        return redirect()
            ->route('topics.index')
            ->withErrors(['image' => 'Falha ao atualizar o tópico. O upload da imagem não foi concluído.']);
    }
}



    // Exclui um tópico
    public function destroy(Topic $topic)
{
    // Caminho da pasta do tópico
    $topicDir = public_path("images/topics/{$topic->id}");

    // Se a pasta existir, remove ela inteira (com a imagem dentro)
    if (File::exists($topicDir)) {
        File::deleteDirectory($topicDir);
    }

    // Exclui o registro do banco
    $topic->delete();

    return redirect()->route('topics.index')->with('msg', 'Tópico excluído com sucesso!');
}

    public function toggleFeatured(Request $request, Topic $topic)
    {
        // Verifica quantos já estão em destaque
        $featuredCount = Topic::where('featured', true)->count();

        // Se o usuário tentar ativar mais de dois
        if ($request->featured && $featuredCount >= 2 && !$topic->featured) {
            return response()->json([
                'success' => false,
                'message' => 'Já existem dois tópicos marcados como destaque.'
            ]);
        }

        // Atualiza o campo
        $topic->featured = $request->featured;
        $topic->save();

        return response()->json(['success' => true]);
    }

}
