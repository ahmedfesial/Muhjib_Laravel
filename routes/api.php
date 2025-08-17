<?php

use App\Http\Controllers\Api\ContactMessageController as ApiContactMessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\MainCategoriesController;
use App\Http\Controllers\SubCategoriesController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\NotificationsController;
use App\Models\ContactMessage;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\QuoteRequestController;
use App\Http\Controllers\QuoteActionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPriceController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\BasketProductsController;
use App\Http\Controllers\PriceUploadLogController;
use App\Http\Controllers\TempletesController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;



// Send email verification link
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth:api']);

// Verify email (via link)
Route::get('/verify-email/{id}', VerifyEmailController::class)
    ->middleware(['auth:api']);

// Send password reset link
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);

// Reset password using token
Route::post('/reset-password', [NewPasswordController::class, 'store']);
// Public Routes
// Route::post('user/create', [UserController::class, 'create']); // Only admin/super-admin can create
Route::post('register', [RegisteredUserController::class, 'store']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::middleware(['auth:api', 'role:super_admin'])->group(function () {
    Route::post('/users/create', [UserController::class, 'create']);
});
// Authenticated Routes
Route::middleware('auth:api')->group(function () {

Route::middleware(['auth:api', 'role:super_admin'])->group(function () {
    Route::delete('products/delete/{product}', [ProductController::class, 'destroy']);

});

Route::middleware(['auth:api', 'role:super_admin,admin'])->group(function () {
// Products Routes
Route::group(['prefix'=>'products'],function(){
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/create', [ProductController::class, 'store']);
    Route::post('update/{product}', [ProductController::class, 'update']);
// Search and Filter Endpoints
    Route::get('products/search', [ProductController::class, 'search']);
    Route::get('products/filter', [ProductController::class, 'filter']);
    // Update Quantity
    Route::patch('products/{product}/update-quantity', [ProductController::class, 'updateQuantity']);
});
});

Route::middleware(['auth:api', 'role:super_admin,admin,user'])->group(function () {
    Route::get('products/show/{product}', [ProductController::class, 'show']);

});


    // User Profile & Dashboard
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile/{user}', [UserController::class, 'updateProfile']);
    Route::get('/dashboard', [UserController::class, 'dashboard']);
    Route::get('/my-clients', [UserController::class, 'showmyclient']);



    Route::middleware('can:destroy,App\Models\User')->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });

    // View all users (admin/super-admin only)
    Route::get('/users', [UserController::class, 'index'])->middleware('can:manageUsers,App\Models\User');

    // Admin Panel Routes
    Route::middleware('can:viewAdminDashboard,App\Models\User')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/admin/users', [AdminController::class, 'manageUsers']);
    });
});



    Route::get('user',[AuthController::class,'user']);
    // Client Routes
Route::group(['prefix' => 'clients'], function () {
    Route::get('/', [ClientsController::class, 'index']);
    Route::post('/create', [ClientsController::class, 'store']);
    Route::get('show/{id}', [ClientsController::class, 'show']);
    Route::post('update/{id}', [ClientsController::class, 'update']);
    Route::delete('delete/{id}', [ClientsController::class, 'destroy']);
    Route::get('my-clients', [UserController::class, 'showmyclient']);
});
// Basket Routes
Route::group(['prefix'=>'baskets'],function(){
Route::get('/', [BasketController::class, 'index']);
    Route::post('/create', [BasketController::class, 'store']);
    Route::get('show/{basket}', [BasketController::class, 'show']);
    Route::post('update/{basket}', [BasketController::class, 'update']);
    Route::delete('delete/{basket}', [BasketController::class, 'destroy']);
    Route::post('/{basket}/status', [BasketController::class, 'changeStatus']);
});
// Product Price Route
Route::group(['prefix'=>'product-prices'],function(){
Route::get('', [ProductPriceController::class, 'index']);
Route::post('/create', [ProductPriceController::class, 'store']);
Route::post('update/{productPrice}', [ProductPriceController::class, 'update']);
Route::delete('delete/{productPrice}', [ProductPriceController::class, 'destroy']);
});

// Basket Product Routes
Route::group(['prefix'=>'basket-products'],function(){
    Route::post('/create', [BasketProductsController::class, 'store']);
    Route::put('update/{basketProduct}', [BasketProductsController::class, 'update']);
    Route::delete('delete/{basketProduct}', [BasketProductsController::class, 'destroy']);
});
// Quote Request Routes
Route::group(['prefix' => 'quote-requests'], function () {
    Route::get('/', [QuoteRequestController::class, 'index']);
    Route::post('/create', [QuoteRequestController::class, 'store']);
    Route::get('show/{id}', [QuoteRequestController::class, 'show']);
    Route::post('update/{id}', [QuoteRequestController::class, 'update']);
});
// Quote Action Routes
Route::group(['prefix' => 'quote-actions'], function () {
    Route::get('/', [QuoteActionController::class, 'index']);
    Route::post('/create', [QuoteActionController::class, 'store']);
});
// Brand routes
Route::group(['prefix' => 'brands'], function () {
    Route::get('/', [BrandController::class, 'index']);
    Route::post('/create', [BrandController::class, 'store']);
    Route::get('/{id}', [BrandController::class, 'show']);
    Route::put('update/{id}', [BrandController::class, 'update']);
    Route::delete('delete/{id}', [BrandController::class, 'destroy']);
});
// Main Categories Routes
Route::group(['prefix' => 'main-categories'], function () {
    Route::get('/', [MainCategoriesController::class, 'index']);
    Route::post('/create', [MainCategoriesController::class, 'store']);
    Route::get('show/{id}', [MainCategoriesController::class, 'show']);
    Route::post('update/{id}', [MainCategoriesController::class, 'update']);
    Route::delete('delete/{id}', [MainCategoriesController::class, 'destroy']);
});
// Sub Categories Routes
Route::group(['prefix' => 'sub-categories'], function () {
    Route::get('/', [SubCategoriesController::class, 'index']);
    Route::post('/create', [SubCategoriesController::class, 'store']);
    Route::get('show/{id}', [SubCategoriesController::class, 'show']);
    Route::put('update/{id}', [SubCategoriesController::class, 'update']);
    Route::delete('delete/{id}', [SubCategoriesController::class, 'destroy']);
});

// Notification Routes
Route::group(['prefix' => 'notifications'], function () {
    Route::get('/', [NotificationsController::class, 'index']);
    Route::post('/create', [NotificationsController::class, 'store']);
    Route::post('/{notification}/mark-as-read', [NotificationsController::class, 'markAsRead']);
});
// Price Logs Routes
Route::group(['prefix'=>'price-upload-logs'],function(){
    Route::get('/', [PriceUploadLogController::class, 'index']);
    Route::post('/create', [PriceUploadLogController::class, 'store']);
    Route::get('show/{priceUploadLog}', [PriceUploadLogController::class, 'show']);
});
// Templates Routes
Route::group(['prefix' => 'templates'], function () {
    Route::get('/', [TempletesController::class, 'index']);
    Route::post('/create', [TempletesController::class, 'store']);
    Route::get('show/{id}', [TempletesController::class, 'show']);
    Route::delete('delete/{id}', [TempletesController::class, 'destroy']);
});
// Catalog Routes
Route::group(['prefix'=>'catalogs'],function(){
Route::get('/', [CatalogController::class, 'index']);
Route::post('/create', [CatalogController::class, 'store']);
Route::get('/show/{catalog}', [CatalogController::class, 'show']);
});
