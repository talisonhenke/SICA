<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::orderBy('name')->paginate(50);
        return view('tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            Tag::create($request->only('name', 'description'));

            DB::commit();
            return redirect()->route('tags.index')->with('success', 'Tag criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e); // debug do erro
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $id,
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Busca a tag pelo ID
            $tag = Tag::findOrFail($id);

            // Atualiza os dados
            $tag->update($request->only('name', 'description'));

            DB::commit();
            return redirect()->route('tags.index')->with('success', 'Tag atualizada!');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e); // debug do erro
        }
    }

    public function destroy($id)
    {
        try {
            $tag = Tag::findOrFail($id); // busca no banco ou dá 404
            $tag->plants()->detach(); // remove relacionamentos na tabela pivot
            $tag->delete(); // remove a tag
            return redirect()->route('tags.index')->with('success', 'Tag removida!');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
