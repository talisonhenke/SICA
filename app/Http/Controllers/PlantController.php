<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Plant;

class PlantController extends Controller
{
    public function index(){
        // $plants = Plant::all();
        $plants = Plant::query()->orderBy('popular_name', 'asc')->get();
        return view('plants.plants_list',['plants' => $plants]);
    }

    public function show($currentId){
        $plants = Plant::where('id', $currentId)->get();
        return view('plants.plant_article',['plants' => $plants]);
    }

    public function create() {
        return view('plants.create');
    }

    public function edit($currentId) {
        $plants = Plant::where('id', $currentId)->get();
        return view('plants.edit',['plants' => $plants]);
    }

    public function store(Request $request) {
        $plant = new Plant;

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
        $plant->tags = $request->tags;
        $plant->qr_code = $request->qr_code;

        // Image upload
        if($request->hasFile('images') && $request->file('images')->isValid()){
            $requestImage = $request->images;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;
            $requestImage->move(public_path('images/plants'), $imageName);
            $plant->images = $imageName;
        }
        else{
            $plant->images = "default.jpg";
        }

        $plant->save();

        return redirect()->route('plants.index')->with('msg', 'Registro adicionado com sucesso!');
    }

    public function update(Request $request, $id) {
    $plant = Plant::findOrFail($id); // Busca a planta existente pelo ID

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
    $plant->tags = $request->tags;
    $plant->qr_code = $request->qr_code;

    // Image upload
    if($request->hasFile('images') && $request->file('images')->isValid()){
        $requestImage = $request->images;
        $extension = $requestImage->extension();
        $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;
        $requestImage->move(public_path('images/plants'), $imageName);
        $plant->images = $imageName;
    }

    $plant->save();

    return redirect()->route('plants.index')->with('msg', 'Registro atualizado com sucesso!');
}

public function destroy($id)
{
    $plant = Plant::findOrFail($id);

    // Se quiser, pode apagar a imagem associada
    if ($plant->images && file_exists(public_path('images/plants/' . $plant->images))) {
        unlink(public_path('images/plants/' . $plant->images));
    }

    $plant->delete();

    return redirect()->route('plants.index')->with('msg', 'Registro apagado com sucesso!');
}

}
