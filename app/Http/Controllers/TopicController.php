<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Primeiro cria o tópico sem a imagem
    $topic = Topic::create([
        'title' => $request->title,
        'description' => $request->description,
        'content' => $request->content,
        'image' => json_encode([]),
    ]);

    // Caminho base: images/topics/{id}/
    $topicDir = public_path("images/topics/{$topic->id}");

    // Cria a pasta se ainda não existir
    if (!file_exists($topicDir)) {
        mkdir($topicDir, 0777, true);
    }

    // Salva a imagem dentro da pasta do ID
    $image = $request->file('image');
    $imageName = time() . '_' . $image->getClientOriginalName();
    $image->move($topicDir, $imageName);

    // Atualiza o campo 'image' com o caminho relativo
    $topic->image = "images/topics/{$topic->id}/{$imageName}";
    $topic->save();

    return redirect()->route('topics.index')->with('msg', 'Tópico criado com sucesso!');
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
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Atualiza os dados de texto
    $topic->title = $request->title;
    $topic->description = $request->description;
    $topic->content = $request->content;

    // Se o usuário enviou uma nova imagem
    if ($request->hasFile('image')) {
    // Caminho da pasta do tópico
    $topicDir = public_path("images/topics/{$topic->id}");

    // Cria a pasta se não existir
    if (!file_exists($topicDir)) {
        mkdir($topicDir, 0777, true);
    }

    // 🔥 Apaga todas as imagens antigas dentro da pasta
    $oldFiles = glob($topicDir . '/*');
    foreach ($oldFiles as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    // Salva a nova imagem
    $image = $request->file('image');
    $imageName = time() . '.' . $image->getClientOriginalExtension();
    $image->move($topicDir, $imageName);

    // Atualiza o caminho no banco
    $topic->image = "images/topics/{$topic->id}/{$imageName}";
}


    $topic->save();

    return redirect()->route('topics.index')->with('msg', 'Tópico atualizado com sucesso!');

}


    // Exclui um tópico
    public function destroy(Topic $topic)
    {
        if ($topic->image && Storage::disk('public')->exists($topic->image)) {
            Storage::disk('public')->delete($topic->image);
        }

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
