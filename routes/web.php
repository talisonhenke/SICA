<?php

use Illuminate\Support\Facades\Route;

// Auth Classes

use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\Auth\LoginController;

// Data Controllers

use App\Http\Controllers\PlantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;

// QR-Code

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Response;

// Models
use App\Models\Topic;


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

// Route::get('/', function(){
//     return view('welcome');
// });
Route::get('/', function () {
    // Pega até 2 tópicos em destaque
    $featuredTopics = Topic::where('featured', true)
        ->latest()
        ->take(2)
        ->get();

    // Se não houver 2 em destaque, pega os últimos 2 criados
    if ($featuredTopics->count() < 2) {
        $featuredTopics = Topic::latest()->take(2)->get();
    }

    return view('welcome', compact('featuredTopics'));
});
Route::get('/plants_list', [PlantController::class, 'index'])->name('plants.index');
Route::get('/plant/{id}/{slug}', [PlantController::class, 'show'])->name('plant.show');
Route::get('/add_plant', [PlantController::class, 'create'])->name('plants.create');
Route::post('/add', [PlantController::class, 'store'])->name('plants.store');
Route::get('/edit_plant/{id}', [PlantController::class, 'edit'])->name('plants.edit');
Route::put('/update/{id}', [PlantController::class, 'update'])->name('plants.update');
Route::delete('/plant/{id}', [PlantController::class, 'destroy'])->name('plants.destroy');
Route::get('/plants/search', [PlantController::class, 'search']); // busca de plantas na homepage


//Login routes

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Rotas para Google Login
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Rota para exibir o formulário de registro
Route::get('register', function () {
    return view('auth.register'); // View de cadastro
})->name('register');

// Rota para processar o cadastro
Route::post('register', [RegisterController::class, 'register'])->name('register.post');

//User routes

Route::get('/users_list', [UserController::class, 'index'])->name('users.index');
Route::patch('/users/{user}/update-level', [UserController::class, 'updateLevel'])->name('users.updateLevel');

// Rotas de edição de perfil

    Route::middleware('auth')->group(function () {
    Route::get('/edit_profile', [App\Http\Controllers\ProfileController::class, 'editProfile'])->name('profile.edit');
    Route::patch('/profile/update-name', [App\Http\Controllers\ProfileController::class, 'updateName'])->name('profile.updateName');
    Route::patch('/profile/update-password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
});

//Topics routes
Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');
Route::get('/topics/create', [TopicController::class, 'create'])->name('topics.create');
Route::post('/topics', [TopicController::class, 'store'])->name('topics.store');
Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show');
Route::get('/topics/{topic}/edit', [TopicController::class, 'edit'])->name('topics.edit');
Route::put('/topics/{topic}', [TopicController::class, 'update'])->name('topics.update');
Route::delete('/topics/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');
Route::post('/topics/{topic}/toggle-featured', [TopicController::class, 'toggleFeatured'])->name('topics.toggleFeatured');

// Products routes

Route::resource('products', ProductController::class);
Route::post('/products/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggleStatus');

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// pix routes 
Route::get('/checkout/pix', [CheckoutController::class, 'pix'])->name('checkout.pix');

// orders routes 
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

Route::get('/orders/{order}/payment', [OrderController::class, 'paymentPage'])
    ->name('orders.payment');




