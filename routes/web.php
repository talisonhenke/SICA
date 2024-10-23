<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PlantController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function(){
    return view('welcome');
});
Route::get('/login', function(){
    return view('login');
});
Route::get('/plants_list', [PlantController::class, 'index'])->name('plants.index');
Route::get('/plant/{id}/{popular_name}', [PlantController::class, 'show'])->name('plants.show');
Route::get('/add_plant', [PlantController::class, 'create'])->name('plants.create');
Route::post('/add', [PlantController::class, 'store'])->name('plants.store');