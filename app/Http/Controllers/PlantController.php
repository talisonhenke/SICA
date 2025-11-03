<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Plant;

class PlantController extends Controller
{
    // Lista todas as plantas
    public function index()
    {
        $plants = Plant::orderBy('popular_name', 'asc')->get();
        return view('plants.plants_list', ['plants' => $plants]);
    }

    // Mostra uma planta específica
    public function show($currentId)
    {
        $plants = Plant::where('id', $currentId)->get();
        return view('plants.plant_article', ['plants' => $plants]);
    }

    // Formulário de criação
    public function create()
    {
        return view('plants.create');
    }

    // Formulário de edição
    public function edit($currentId)
    {
        $plants = Plant::where('id', $currentId)->get();
        return view('plants.edit', ['plants' => $plants]);
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

    // Cria um novo registro
    public function store(Request $request)
{
    // dd($request->file('images'));

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
    $plant->qr_code = $request->qr_code;
    $plant->images = json_encode([]); // valor padrão temporário

    // Salva primeiro para gerar o ID
    $plant->save();

    $imagePaths = [];

    // Criação da pasta + upload de imagens
    if ($request->hasFile('images')) {
        $dir = public_path('images/plants/' . $plant->id);

        // Cria a pasta se não existir
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        foreach ($request->file('images') as $image) {
            if ($image->isValid()) {
                $extension = $image->extension();
                $imageName = md5($image->getClientOriginalName() . microtime(true)) . '.' . $extension;
                $image->move($dir, $imageName);
                $imagePaths[] = 'images/plants/' . $plant->id . '/' . $imageName;
            }
        }
    }

    // Atualiza o campo images e salva novamente
    if (!empty($imagePaths)) {
        $plant->images = json_encode($imagePaths);
        $plant->save();
    }

    return redirect()->route('plants.index')->with('msg', 'Registro adicionado com sucesso!');
}

    // atualiza uma planta
    public function update(Request $request, $id)
{
// DEBUG: inspeciona os arquivos e todos os dados do request
// dd([
//     'hasFile_images' => $request->hasFile('images'),
//     'allFiles' => $request->allFiles(),          // mostra todos os arquivos recebidos (array)
//     'file_images' => $request->file('images'),   // geralmente array ou null
//     'all' => $request->all(),                    // mostra campos hidden (deleted_images, ordered_images, etc)
//     '_method' => $request->_method ?? null,      // confirma spoofed method
// ]);

    $plant = Plant::findOrFail($id);

    // Atualiza os dados principais
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
    $plant->qr_code = $request->qr_code;

    // Decodifica imagens existentes (garantindo array válido)
    $existingImages = json_decode($plant->images, true);
    if (!is_array($existingImages)) {
        $existingImages = [];
    }

    // Caminho da pasta específica
    $dir = public_path('images/plants/' . $plant->id);

    // Cria a pasta caso não exista
    if (!File::exists($dir)) {
        File::makeDirectory($dir, 0755, true);
    }

    $imagesUpdated = false; // flag para saber se houve mudança

    // Remove imagens marcadas para exclusão
    $deletedImages = json_decode($request->deleted_images ?? '[]', true);
    if (is_array($deletedImages) && !empty($deletedImages)) {
        foreach ($deletedImages as $imagePath) {
            $fullPath = public_path($imagePath);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
            $existingImages = array_filter($existingImages, fn($img) => $img !== $imagePath);
        }
        $imagesUpdated = true;
    }

    // Upload de novas imagens (adiciona às existentes)
if ($request->hasFile('images')) {
    foreach ($request->file('images') as $image) {
        if ($image->isValid()) {
            $extension = $image->extension();
            $imageName = md5($image->getClientOriginalName() . microtime(true)) . '.' . $extension;
            $image->move($dir, $imageName);
            $newImagePath = 'images/plants/' . $plant->id . '/' . $imageName;

            // ✅ Adiciona ao array de existentes
            $existingImages[] = $newImagePath;
            $imagesUpdated = true;
        }
    }
}

// Reordena imagens, se vier ordenação via input hidden
if ($request->has('ordered_images')) {
    $ordered = json_decode($request->ordered_images, true);

    if (is_array($ordered) && !empty($ordered)) {
        // Inclui as imagens novas (caso não estejam no array de ordenação)
        $merged = array_unique(array_merge($ordered, $existingImages));
        $existingImages = array_values($merged);
        $imagesUpdated = true;
    }
}


    // Só atualiza o campo se houve modificação nas imagens
    if ($imagesUpdated) {
        $plant->images = json_encode(array_values($existingImages));
    }

    // Salva tudo
    $plant->save();

    return redirect()->route('plants.index')->with('msg', 'Registro atualizado com sucesso!');
}


    // Exclui uma planta e suas imagens
    public function destroy($id)
    {
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

        // Remove o diretório inteiro da planta
        $dir = public_path('images/plants/' . $plant->id);
        if (File::exists($dir)) {
            File::deleteDirectory($dir);
        }

        $plant->delete();

        return redirect()->route('plants.index')->with('msg', 'Registro apagado com sucesso!');
    }
}
