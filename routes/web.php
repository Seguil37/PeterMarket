<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\Product;

use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AuthController;

// Admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\InventoryController; // <- si usas el módulo de inventario nuevo
use App\Http\Controllers\Admin\AdminUserController;

/* =====================  CATÁLOGO (HOME)  ===================== */
Route::get('/', function (Request $request) {
    $q    = trim((string) $request->query('q', ''));
    $sort = (string) $request->query('sort', 'name_asc');
    $category = trim((string) $request->query('category', ''));

    $query = Product::select('id','name','price','image_url','stock','category_type');
    if ($q !== '') $query->where('name','like',"%{$q}%");
    if ($category !== '') $query->where('category_type', $category);

    $query->when($sort === 'price_asc',  fn($q) => $q->orderBy('price'))
          ->when($sort === 'price_desc', fn($q) => $q->orderBy('price','desc'))
          ->when($sort === 'stock_desc', fn($q) => $q->orderBy('stock','desc'))
          ->when(!in_array($sort,['price_asc','price_desc','stock_desc']), fn($q) => $q->orderBy('name'));

    $products = $query->paginate(12)->withQueryString();
    $categories = Product::select('category_type')->distinct()->orderBy('category_type')->pluck('category_type');

    return view('welcome', compact('products','q','sort','categories','category'));
})->name('catalog.index');

Route::get('/products/{product}', [CatalogController::class, 'show'])->name('catalog.show');

/* =====================  CARRITO + CHECKOUT  ===================== */
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{productId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{productId}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/order/success/{order}', [CheckoutController::class, 'success'])->name('order.success');

/* =====================  PÁGINA ESTÁTICA  ===================== */
Route::view('/nosotros', 'pages.about')->name('about');

/* =====================  AUTH  ===================== */
// Registro de clientes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showCustomerLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'customerLogin'])->name('login.post');

    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/* =====================  ADMIN (ÚNICO GRUPO, PROTEGIDO)  ===================== */
Route::middleware(['auth','admin'])
    ->prefix('admin')->as('admin.')
    ->group(function () {
        // Si DashboardController es invocable:
        Route::get('/', DashboardController::class)->name('dashboard');
        // Si NO es invocable, usa:
        // Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // INVENTARIO (módulo nuevo)
        Route::get('/inventario',  [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventario', [InventoryController::class, 'store'])->name('inventory.store');
        Route::delete('/inventario/{inventory}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

        // CRUD de productos
        Route::resource('products', AdminProductController::class);

        // Gestión de administradores solo para Admin Master
        Route::middleware('master')->group(function () {
            Route::resource('admins', AdminUserController::class)->except(['show']);
        });
    });

/* ===== (Opcional) si quieres mantener tus rutas antiguas públicas de COMPRAS =====
use App\Http\Controllers\CompraController;
Route::get('/compras',  [CompraController::class, 'index'])->name('compras.index');
Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
*/
