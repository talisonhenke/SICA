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

    public function store(Request $request) {
        $plant = new Plant;

        $plant->popular_name = $request->popular_name;
    }
}
