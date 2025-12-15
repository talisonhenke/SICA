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
use App\Http\Controllers\TopicCommentController;
use App\Http\Controllers\PlantCommentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AddressesController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiteReviewController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ModerationPanelController;
use App\Http\Controllers\OrderPanelController;


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
    $featuredTopics = Topic::where('featured', true)->latest()->take(2)->get();

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
    Route::patch('/profile/update-phone', [ProfileController::class, 'updatePhone'])->name('profile.updatePhone');
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

// Comentários de tópicos
Route::middleware(['auth'])->group(function () {
    Route::post('/topics/{topic}/comments', [TopicCommentController::class, 'store'])->name('topics.comments.store');

    Route::put('/topic-comments/{comment}', [TopicCommentController::class, 'update'])->name('topic-comments.update');

    Route::delete('/topic-comments/{comment}', [TopicCommentController::class, 'destroy'])->name('topic-comments.destroy');

    Route::delete('/admin/comments/{id}/moderate-delete', [TopicCommentController::class, 'moderateDelete'])
        ->middleware(['auth', 'is_admin'])
        ->name('topic-comments.moderateDelete');

    Route::post('/topic-comments/{id}/report', [TopicCommentController::class, 'report'])->name('topic-comments.report');

    Route::post('/topic-comments/{id}/allow', [TopicCommentController::class, 'allow'])->name('topic-comments.allow');

    // BLOQUEAR COMENTÁRIOS DO USUÁRIO
    Route::post('/topic-comments/block-user/{userId}', [TopicCommentController::class, 'blockUser'])->name('topic-comments.blockUser');
});

// Products routes

Route::resource('products', ProductController::class);
Route::post('/products/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggleStatus');

// Comentários de plantas
Route::middleware(['auth'])->group(function () {
    // Criar novo comentário em uma planta
    Route::post('/plants/{plant}/comments', [PlantCommentController::class, 'store'])->name('plant-comments.store');

    // Atualizar comentário de planta
    Route::put('/plant-comments/{comment}', [PlantCommentController::class, 'update'])->name('plant-comments.update');

    // Excluir comentário de planta
    Route::delete('/plant-comments/{comment}', [PlantCommentController::class, 'destroy'])->name('plant-comments.destroy');

    // Exclusão moderada pelo admin
    Route::delete('/admin/plant-comments/{id}/moderate-delete', [PlantCommentController::class, 'moderateDelete'])
        ->middleware(['auth', 'is_admin'])
        ->name('plant-comments.moderateDelete');

    // Reportar comentário
    Route::post('/plant-comments/{id}/report', [PlantCommentController::class, 'report'])->name('plant-comments.report');

    // Permitir comentário após análise do admin
    Route::post('/plant-comments/{id}/allow', [PlantCommentController::class, 'allow'])->name('plant-comments.allow');

    // Bloquear usuário de comentar em plantas
    Route::post('/plant-comments/block-user/{userId}', [PlantCommentController::class, 'blockUser'])->name('plant-comments.blockUser');
});

// Cart routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::patch('/cart/update/{id}', [CartController::class, 'updateQuantity'])->name('cart.update');

// pix routes
Route::get('/checkout/pix', [CheckoutController::class, 'pix'])->name('checkout.pix');

// orders routes
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

Route::get('/orders/{order}/payment', [OrderController::class, 'paymentPage'])->name('orders.payment');

Route::get('/meus-pedidos', [OrderController::class, 'index'])->name('orders.index');

Route::get('/meus-pedidos/{id}', [OrderController::class, 'show'])->name('orders.show');

// addresses routes

Route::middleware(['auth'])->group(function () {
    // Lista endereços
    Route::get('/addresses', [AddressesController::class, 'index'])->name('addresses.index');

    // Página de criação
    Route::get('/addresses/create', [AddressesController::class, 'create'])->name('addresses.create');

    // Salvar novo endereço
    Route::post('/addresses', [AddressesController::class, 'store'])->name('addresses.store');

    // Mostrar endereço específico
    Route::get('/addresses/{address}', [AddressesController::class, 'show'])->name('addresses.show');

    // Página de edição
    Route::get('/addresses/{address}/edit', [AddressesController::class, 'edit'])->name('addresses.edit');

    // Atualizar endereço
    Route::put('/addresses/{address}', [AddressesController::class, 'update'])->name('addresses.update');

    // Deletar endereço
    Route::delete('/addresses/{address}', [AddressesController::class, 'destroy'])->name('addresses.destroy');
});

Route::patch('/addresses/{address}/primary', [AddressesController::class, 'setPrimary'])->name('addresses.setPrimary');

Route::post('/addresses/storeByCheckout', [AddressesController::class, 'storeByCheckout'])->name('addresses.storeByCheckout');

// Admin Order routes

Route::prefix('admin')
    ->middleware(['auth', 'is_admin'])
    ->group(function () {
        Route::get('/orders', [OrderAdminController::class, 'index'])->name('admin.orders.index');

        // Página individual do pedido
        Route::get('/orders/{id}', [OrderAdminController::class, 'show'])->name('admin.orders.show');

        // 1. Marcar como pago (pending → preparing)
        Route::post('/orders/{id}/mark-paid', [OrderAdminController::class, 'markPaid'])->name('admin.orders.markPaid');

        // 2. Enviar pedido (preparing → shipped)
        Route::post('/orders/{id}/ship', [OrderAdminController::class, 'ship'])->name('admin.orders.ship');

        // 3. Cancelar pedido (qualquer estado permitido)
        Route::post('/orders/{id}/cancel', [OrderAdminController::class, 'cancel'])->name('admin.orders.cancel');

        //4. Pedido entregue (shippded → delivered)
        Route::post('/admin/orders/{id}/deliver', [OrderAdminController::class, 'deliver'])->name('admin.orders.deliver');
    });

// Admim Dashboard routes

Route::middleware(['auth', 'is_admin'])
    ->prefix('admin/ajax')
    ->name('admin.ajax.')
    ->group(function () {
        // DASHBOARD
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // PEDIDOS
        Route::get('/orders', [OrderAdminController::class, 'index'])->name('orders.index');

        // MODERAÇÃO
        Route::get('/moderation', [AdminController::class, 'moderationIndex'])->name('moderation.index');

        // TAGS
        Route::get('/tags', [TagController::class, 'index'])->name('tags.index');

        // // USUÁRIOS
        // Route::get('/users', [AdminUserController::class, 'index'])
        //     ->name('users.index');
    });

    // PEDIDOS ajax 
    Route::middleware(['auth', 'is_admin'])
    ->prefix('admin/panels')
    ->name('admin.panels.')
    ->group(function () {

        Route::get('/orders', [OrderPanelController::class, 'index'])
            ->name('orders');

    });

Route::get('/orders/{order}', [OrderAdminController::class, 'orderModal'])->name('admin.orders.ajax.modal');

Route::post('/orders/ajax/{order}/mark-paid', [OrderAdminController::class, 'ajaxMarkPaid'])->name('admin.orders.ajax.markPaid');

Route::post('/orders/ajax/{order}/ship', [OrderAdminController::class, 'ajaxShip'])->name('admin.orders.ajax.ship');

Route::post('/orders/ajax/{order}/cancel', [OrderAdminController::class, 'ajaxCancel'])->name('admin.orders.ajax.cancel');

// Atualizações moderação

Route::get('/api/check-updates', [AdminController::class, 'checkUpdates'])->name('admin.checkUpdates');

Route::get('/api/counters', [AdminController::class, 'counters'])->name('admin.counters');

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/moderation', [AdminController::class, 'moderationIndex'])->name('admin.moderation.index');
});

// Moderação ajax

Route::get('/admin/panels/moderation', [ModerationPanelController::class, 'index'])->name('admin.panels.moderation');

Route::get('/admin/dashboard/moderation-panel', [AdminDashboardController::class, 'moderationPanelAjax'])->name('admin.dashboard.moderation.ajax');

Route::delete('/admin/comments/{id}/ajax/moderate-delete', [TopicCommentController::class, 'moderateDeleteAjax'])
    ->middleware(['auth', 'is_admin'])
    ->name('topic-comments.ajax.moderateDelete');

Route::post('/topic-comments/ajax/block-user/{userId}', [TopicCommentController::class, 'blockUserAjax'])
    ->middleware(['auth', 'is_admin'])
    ->name('topic-comments.ajax.blockUser');

Route::post('/topic-comments/{id}/ajax/allow', [TopicCommentController::class, 'allowAjax'])
    ->middleware(['auth', 'is_admin'])
    ->name('topic-comments.ajax.allow');

// Rotas de avaliação do site

Route::middleware(['auth'])->group(function () {
    Route::post('/review', [SiteReviewController::class, 'store'])->name('review.store');
    Route::post('/review/update', [SiteReviewController::class, 'update'])->name('review.update');
});
Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin/reviews', [SiteReviewController::class, 'adminIndex'])->name('admin.reviews.index');
});

// Tags routes

Route::middleware(['auth', 'is_admin'])->group(function () {
    // LISTAR (index)
    Route::get('/admin/tags', [TagController::class, 'index'])->name('tags.index');

    // CRIAR (store)
    Route::post('/admin/tags', [TagController::class, 'store'])->name('tags.store');

    // EDITAR (update)
    Route::put('/admin/tags/{id}', [TagController::class, 'update'])->name('tags.update');

    // EXCLUIR (destroy)
    Route::delete('/admin/tags/{id}', [TagController::class, 'destroy'])->name('tags.destroy');
});
