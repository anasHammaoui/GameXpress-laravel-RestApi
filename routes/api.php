<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;
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
Route::middleware(['auth:sanctum','role:product_manager']) -> group(function (){
    Route::get('/v1/admin/products',[ProductController::class, 'index']);
    Route::get('/v1/admin/products/{product}',[ProductController::class, 'show']);
    Route::post('/v1/admin/products',[ProductController::class,'store']);
    Route::put('/v1/admin/products/{product}',[ProductController::class,'update']);
    Route::delete('/v1/admin/products/{id}',[ProductController::class, 'destroy']);
    // categories controller
    Route::resource('/v1/admin/categories', CategoryController::class);
});
// user manager routes
Route::resource('/v1/admin/users',UserManageController::class) -> middleware(['auth:sanctum','role:user_manager']);