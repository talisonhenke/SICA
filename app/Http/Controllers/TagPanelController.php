<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TagPanelController extends Controller
{
    public function index(Request $request)
    {
        $tags = Tag::orderBy('name')->get();

        return view('admin.dashboard.panels.tags', compact('tags'));
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

        return response()->json([
            'success' => true,
            'message' => 'Tag criada com sucesso!'
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Erro ao criar tag.'
        ], 500);
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

        $tag = Tag::findOrFail($id);
        $tag->update($request->only('name', 'description'));

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Tag atualizada com sucesso!'
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Erro ao atualizar tag.'
        ], 500);
    }
}

public function destroy($id)
{
    try {
        DB::beginTransaction();

        $tag = Tag::findOrFail($id);
        $tag->plants()->detach();
        $tag->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Tag removida com sucesso!'
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Erro ao remover tag.'
        ], 500);
    }
}

}
