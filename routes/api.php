<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\UserAuthController;
use App\Http\Controllers\Api\V1\UserManageController;
use App\Http\Controllers\Api\V2\StockController;
use App\Http\Controllers\Api\V2\CartController;
use App\Http\Controllers\Api\V2\AdminController;
use App\Http\Controllers\Api\V3\PaymentController;
use App\Http\Controllers\Api\V3\CommandController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;
// ********************************V1***********************
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// auth routes
Route::post('/v1/admin/login',[UserAuthController::class,"login"]) -> name('login');
Route::post('/v1/admin/register',[UserAuthController::class,"register"]) -> name('register');
Route::post('/v1/admin/logout',[UserAuthController::class,"logout"]) -> middleware('auth:sanctum');
// dashboaord routes 
Route::get('/v1/admin/dashboard',[DashboardController::class, 'index']) 
-> middleware(['auth:sanctum','role:super_admin|product_manager|users_manager']);
// product managers routes
Route::middleware(['auth:sanctum','role:super_admin|product_manager']) -> group(function (){
    Route::post('/v1/admin/products ',[ProductController::class,'store']);
    Route::put('/v1/admin/products/{product}',[ProductController::class,'update']);
    Route::delete('/v1/admin/products/{id}',[ProductController::class, 'destroy']);
    // categories controller
    Route::resource('/v1/admin/categories', CategoryController::class);
});
Route::get('/v1/admin/products',[ProductController::class, 'index']);
Route::get('/v1/admin/products/{product}',[ProductController::class, 'show']);
// user manager routes
Route::resource('/v1/admin/users',UserManageController::class) -> middleware(['auth:sanctum','role:user_manager']);

// *************************************************************V2********************


//route pour la fonction de ceomparation de stock
Route::get('/v2/admin/stock/{product_id}/{quantity}',[StockController::class,'compareToStock']);

//route pour la fonction de fusion de panier
Route::get('/v2/admin/merge/{sessionId}',[StockController::class,'mergeGuestCart']);


// anas
Route::post('v2/admin/users/roles/{user}',[AdminController::class,'changeRole']) -> middleware(['auth:sanctum','role:super_admin']);

Route::get("/v2/client/cart/{userId}",[CartController::class, 'cartDetails']);


// mohammed
Route::post('/v2/admin/assign-permissions/{user_id}',[AdminController::class,'assignPermissions']) -> middleware(['auth:sanctum','role:super_admin']);

Route::post('/v2/client/addtocart',[CartController::class,'store']);
Route::get('/v2/client/cart',[CartController::class,'index']) -> middleware('auth:sanctum', 'role:client');

Route::put('/v2/client/cart/{cart_id}',[CartController::class,'update']);
Route::delete('/v2/client/cart/{cart_id}',[CartController::class,'destroy']);

// add  new route for cart details
Route::get('/v2/client/cart-details/{user_id}', [CartController::class, 'cartDetails'])
    ->middleware(['auth:sanctum', 'role:client']);

// ******************   V3  ******************
// payment
    Route::post("/v3/client/cart/payment",[PaymentController::class, "createCheckoutSession"]);
    Route::get('/v3/client/payment/success', [PaymentController::class, 'success']);
    Route::get('/v3/client/payment/cancel',[PaymentController::class, 'cancel']);
// Orders

Route::post('/v3/client/order',[CommandController::class,'create']) -> middleware(['auth:sanctum','role:client']);
Route::get('/v3/admin/orders',[CommandController::class,'index']) -> middleware('auth:sanctum', 'role:super_admin');
Route::get('/v3/admin/orders/{id}',[CommandController::class,'show']) -> middleware('auth:sanctum', 'role:super_admin');
Route::put('/v3/admin/orders/{id}/status',[CommandController::class,'update']) -> middleware('auth:sanctum', 'role:super_admin');
Route::delete('/v3/admin/orders/{id}',[CommandController::class,'destroy']) -> middleware('auth:sanctum', 'role:super_admin');

//transations
Route::get('/v3/admin/transactions', [PaymentController::class, 'transactions'])->middleware(['auth:sanctum','role:super_admin']);

Route::get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user(),
        'roles' => $request->user()->getRoleNames()->toArray(),
        'permissions' => $request->user()->getAllPermissions()->pluck('name')->toArray()
    ]);
})->middleware('auth:sanctum');