<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Plant;

class PlantController extends Controller
{
    public function index(){
        // $plants = Plant::all();
        $plants = Plant::query()->orderBy('popular_name', 'asc')->get();
        return view('plants_list',['plants' => $plants]);
    }

    public function find(){
        $plants = Plant::where('id', 2)->get();
        // echo $plant;
        return view('plant_article',['plants' => $plants]);
    }

    public function create() {
        return view('plants.create');
    }
}
