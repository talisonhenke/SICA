<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Plant;
use App\Models\Product;
use App\Models\Tag;

class PlantController extends Controller
{
    // Lista todas as plantas
    public function index()
    {
        $plants = Plant::orderBy('popular_name', 'asc')->get();
        return view('plants.plants_list', ['plants' => $plants]);
    }

    // Mostra uma planta específica
    public function show($id, $slug = null)
    {
        $plant = Plant::findOrFail($id);
        $product = Product::where('plant_id', $plant->id)->first();
        $actualSlug = $plant->slug ?? Str::slug($plant->popular_name, '-');

        if ($slug === null || $slug !== $actualSlug) {
            return redirect()->to(url("/plant/{$plant->id}/{$actualSlug}"));
        }

        return view('plants.plant_article', compact('plant', 'product'));
    }

    // Formulário de criação
    public function create()
    {
        $tags = Tag::all(); // pega todas as tags disponíveis

        return view('plants.create', compact('tags'));
    }

    // Formulário de edição
    public function edit($id)
    {
        $plant = Plant::findOrFail($id);

        // Pega todas as tags existentes
        $tags = Tag::orderBy('name', 'asc')->get();

        // Pega os IDs das tags já associadas a esta planta
        $selectedTags = $plant->tags()->pluck('tags.id')->toArray();

        return view('plants.edit', [
            'plant' => $plant,
            'tags' => $tags,
            'selectedTags' => $selectedTags,
        ]);
    }

    // Busca por nome científico ou popular
    public function search(Request $request)
    {
        $query = $request->input('q');
        $plants = Plant::where('popular_name', 'LIKE', "%{$query}%")
            ->orWhere('scientific_name', 'LIKE', "%{$query}%")
            ->get(['id', 'popular_name', 'scientific_name']);

        return response()->json($plants);
    }

    // ✅ Cria um novo registro
    public function store(Request $request)
    {
        $request->validate(
            [
                'scientific_name' => 'required|string|max:255',
                'popular_name' => 'required|string|max:255',
                'habitat' => 'required|string',
                'useful_parts' => 'required|array|min:1',
                'characteristics' => 'required|string',
                'observations' => 'required|string',
                'popular_use' => 'required|string',
                'chemical_composition' => 'required|string',
                'contraindications' => 'required|string',
                'mode_of_use' => 'required|string',
                'info_references' => 'required|string',
                // Imagens inicialmente nullable para criar o registro sem problema e gerar o id da planta
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'tags' => 'nullable|string',
            ],
            [
                // Campos principais
                'scientific_name.required' => 'O nome científico é obrigatório.',
                'scientific_name.string' => 'O nome científico deve conter apenas texto.',
                'scientific_name.max' => 'O nome científico não pode ultrapassar 255 caracteres.',

                'popular_name.required' => 'O nome popular é obrigatório.',
                'popular_name.string' => 'O nome popular deve conter apenas texto.',
                'popular_name.max' => 'O nome popular não pode ultrapassar 255 caracteres.',

                'habitat.required' => 'O campo habitat é obrigatório.',
                'habitat.string' => 'O habitat deve ser um texto válido.',

                // Partes úteis
                'useful_parts.required' => 'Selecione ao menos uma parte útil da planta.',
                'useful_parts.array' => 'O campo partes úteis deve ser uma lista válida.',
                'useful_parts.min' => 'Escolha pelo menos uma parte útil.',

                // Textos descritivos
                'characteristics.required' => 'O campo características é obrigatório.',
                'characteristics.string' => 'As características devem ser um texto válido.',

                'observations.required' => 'O campo observações é obrigatório.',
                'observations.string' => 'As observações devem ser um texto válido.',

                'popular_use.required' => 'O campo uso popular é obrigatório.',
                'popular_use.string' => 'O uso popular deve ser um texto válido.',

                'chemical_composition.required' => 'O campo composição química é obrigatório.',
                'chemical_composition.string' => 'A composição química deve ser um texto válido.',

                'contraindications.required' => 'O campo contraindicações é obrigatório.',
                'contraindications.string' => 'As contraindicações devem ser um texto válido.',

                'mode_of_use.required' => 'O campo modos de uso é obrigatório.',
                'mode_of_use.string' => 'Os modos de uso devem ser um texto válido.',

                'info_references.required' => 'O campo referências é obrigatório.',
                'info_references.string' => 'As referências devem ser um texto válido.',

                // Imagens
                'images.*.image' => 'Cada arquivo enviado deve ser uma imagem válida.',
                'images.*.mimes' => 'As imagens devem estar nos formatos: JPEG, PNG, JPG ou WEBP.',
                'images.*.max' => 'Cada imagem não pode ultrapassar 5 MB de tamanho.',

                //Tags
                'tags.string' => 'Formato inválido para tags.',
            ],
        );

        DB::beginTransaction();

        try {
            $plant = new Plant();
            $plant->scientific_name = $request->scientific_name;
            $plant->popular_name = $request->popular_name;
            $plant->habitat = $request->habitat;
            $plant->useful_parts = $request->useful_parts;
            $plant->characteristics = $request->characteristics;
            $plant->observations = $request->observations;
            $plant->popular_use = $request->popular_use;
            $plant->chemical_composition = $request->chemical_composition;
            $plant->contraindications = $request->contraindications;
            $plant->mode_of_use = $request->mode_of_use;
            $plant->info_references = $request->info_references;
            $plant->images = json_encode([]); // placeholder
            $plant->slug = Str::slug($plant->popular_name, '-');

            $plant->save();

            // QR code automático se não informado
            $plant->qr_code = $request->qr_code ?: url("/plant/{$plant->id}/{$plant->slug}");

            $imagePaths = [];

            // Upload de imagens (se houver)
            if ($request->hasFile('images')) {
                $dir = public_path('images/plants/' . $plant->id);
                if (!File::exists($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }

                foreach ($request->file('images') as $image) {
                    if ($image && $image->isValid()) {
                        $extension = $image->extension();
                        $imageName = md5($image->getClientOriginalName() . microtime(true)) . '.' . $extension;
                        $image->move($dir, $imageName);
                        $imagePaths[] = 'images/plants/' . $plant->id . '/' . $imageName;
                    }
                }
            }

            if (!empty($imagePaths)) {
                $plant->images = json_encode($imagePaths);
            }

            $plant->save();

            // Processa tags
            if ($request->filled('tags')) {
                $tagIds = explode(',', $request->tags); // converte string em array
                $tagIds = array_map('intval', $tagIds); // garante que sejam inteiros
                $plant->tags()->sync($tagIds);
            }

            DB::commit();

            return redirect()->route('plants.index')->with('msg', '✅ Planta cadastrada com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();

            // Tenta remover arquivos/pasta parcialmente criados
            try {
                if (isset($plant) && $plant->id) {
                    $dir = public_path('images/plants/' . $plant->id);
                    if (File::exists($dir)) {
                        File::deleteDirectory($dir);
                    }
                }
            } catch (\Throwable $inner) {
                Log::warning('Falha ao limpar diretório após erro: ' . $inner->getMessage());
            }

            Log::error('Erro ao salvar planta: ' . $e->getMessage(), ['stack' => $e->getTraceAsString()]);

            return back()
                ->withErrors(['error' => '❌ Erro ao salvar a planta. Tente novamente.'])
                ->withInput();
        }
    }

    // ✅ Atualiza uma planta existente
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'scientific_name' => 'required|string|max:255',
                'popular_name' => 'required|string|max:255',
                'habitat' => 'required|string',
                'useful_parts' => 'required|array|min:1',
                'characteristics' => 'required|string',
                'observations' => 'required|string',
                'popular_use' => 'required|string',
                'chemical_composition' => 'required|string',
                'contraindications' => 'required|string',
                'mode_of_use' => 'required|string',
                'info_references' => 'required|string',
                // Imagens nullable pois nesse caso geralmente já tem imagem no banco de dados e o usuário pode não querer alterá-la
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'tags' => 'nullable|string',
            ],
            [
                // Campos principais
                'scientific_name.required' => 'O nome científico é obrigatório.',
                'scientific_name.string' => 'O nome científico deve conter apenas texto.',
                'scientific_name.max' => 'O nome científico não pode ultrapassar 255 caracteres.',

                'popular_name.required' => 'O nome popular é obrigatório.',
                'popular_name.string' => 'O nome popular deve conter apenas texto.',
                'popular_name.max' => 'O nome popular não pode ultrapassar 255 caracteres.',

                'habitat.required' => 'O campo habitat é obrigatório.',
                'habitat.string' => 'O habitat deve ser um texto válido.',

                // Partes úteis
                'useful_parts.required' => 'Selecione ao menos uma parte útil da planta.',
                'useful_parts.array' => 'O campo partes úteis deve ser uma lista válida.',
                'useful_parts.min' => 'Escolha pelo menos uma parte útil.',

                // Textos descritivos
                'characteristics.required' => 'O campo características é obrigatório.',
                'characteristics.string' => 'As características devem ser um texto válido.',

                'observations.required' => 'O campo observações é obrigatório.',
                'observations.string' => 'As observações devem ser um texto válido.',

                'popular_use.required' => 'O campo uso popular é obrigatório.',
                'popular_use.string' => 'O uso popular deve ser um texto válido.',

                'chemical_composition.required' => 'O campo composição química é obrigatório.',
                'chemical_composition.string' => 'A composição química deve ser um texto válido.',

                'contraindications.required' => 'O campo contraindicações é obrigatório.',
                'contraindications.string' => 'As contraindicações devem ser um texto válido.',

                'mode_of_use.required' => 'O campo modos de uso é obrigatório.',
                'mode_of_use.string' => 'Os modos de uso devem ser um texto válido.',

                'info_references.required' => 'O campo referências é obrigatório.',
                'info_references.string' => 'As referências devem ser um texto válido.',

                // Imagens
                'images.*.image' => 'Cada arquivo enviado deve ser uma imagem válida.',
                'images.*.mimes' => 'As imagens devem estar nos formatos: JPEG, PNG, JPG ou WEBP.',
                'images.*.max' => 'Cada imagem não pode ultrapassar 5 MB de tamanho.',
            ],
        );

        DB::beginTransaction();

        try {
            $plant = Plant::findOrFail($id);

            $plant->fill([
                'scientific_name' => $request->scientific_name,
                'popular_name' => $request->popular_name,
                'habitat' => $request->habitat,
                'useful_parts' => $request->useful_parts,
                'characteristics' => $request->characteristics,
                'observations' => $request->observations,
                'popular_use' => $request->popular_use,
                'chemical_composition' => $request->chemical_composition,
                'contraindications' => $request->contraindications,
                'mode_of_use' => $request->mode_of_use,
                'info_references' => $request->info_references,
                'slug' => Str::slug($request->popular_name, '-'),
            ]);

            $plant->qr_code = $request->qr_code ? $request->qr_code : url("/plant/{$plant->id}/{$plant->slug}");

            // Gerencia imagens
            $existingImages = json_decode($plant->images, true) ?? [];
            $dir = public_path('images/plants/' . $plant->id);

            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            // Remove imagens deletadas
            $deletedImages = json_decode($request->deleted_images ?? '[]', true);
            if (is_array($deletedImages) && !empty($deletedImages)) {
                foreach ($deletedImages as $imagePath) {
                    $fullPath = public_path($imagePath);
                    if (File::exists($fullPath)) {
                        File::delete($fullPath);
                    }
                    $existingImages = array_filter($existingImages, fn($img) => $img !== $imagePath);
                }
            }

            // Upload de novas imagens
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image->isValid()) {
                        $extension = $image->extension();
                        $imageName = md5($image->getClientOriginalName() . microtime(true)) . '.' . $extension;
                        $image->move($dir, $imageName);
                        $existingImages[] = 'images/plants/' . $plant->id . '/' . $imageName;
                    }
                }
            }

            // Ordena imagens se enviado
            if ($request->has('ordered_images')) {
                $ordered = json_decode($request->ordered_images, true);
                if (is_array($ordered) && !empty($ordered)) {
                    $existingImages = array_values(array_unique(array_merge($ordered, $existingImages)));
                }
            }

            $plant->images = json_encode(array_values($existingImages));
            $plant->save();

            $tagIds = array_filter(explode(',', $request->input('tags', '')));
            $plant->tags()->sync($tagIds);

            DB::commit();
            return redirect()->route('plants.index')->with('msg', '✅ Planta atualizada com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar planta: ' . $e->getMessage(), ['stack' => $e->getTraceAsString()]);

            // DEBUG: mostrar erro completo na tela
            dd([
                'Mensagem' => $e->getMessage(),
                'Stack Trace' => $e->getTraceAsString(),
            ]);
            //return back()->withErrors(['error' => '❌ Erro ao atualizar a planta.'])->withInput();
        }
    }

    // Exclui planta e suas imagens
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $plant = Plant::findOrFail($id);

            if ($plant->images) {
                $images = json_decode($plant->images, true);
                foreach ($images as $img) {
                    $path = public_path($img);
                    if (File::exists($path)) {
                        File::delete($path);
                    }
                }
            }

            $dir = public_path('images/plants/' . $plant->id);
            if (File::exists($dir)) {
                File::deleteDirectory($dir);
            }

            $plant->delete();
            DB::commit();

            return redirect()->route('plants.index')->with('msg', '🗑️ Planta excluída com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao excluir planta: ' . $e->getMessage(), ['stack' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => '❌ Erro ao excluir a planta.']);
        }
    }
}
