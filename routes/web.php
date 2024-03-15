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

Route::get('/', [PlantController::class, 'index']);
// Route::get('/', function(){
//     return view('welcome');
// });
// Route::get('/plants_list', 'PlantController@index')->name('plants.index');
Route::get('/plants_list', [PlantController::class, 'index']);
Route::get('/plant/{id}/{popular_name}', [PlantController::class, 'find']);

