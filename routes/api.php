<?php

use App\Http\Controllers\API\Auth\AdminController;
use App\Http\Controllers\API\Auth\UserController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/****************************************************************************************
 *                        -----     Auth Modules     -----
 ***************************************************************************************/
Route::controller(UserController::class)->group(function () {
    Route::post('/auth/register', 'register');
    Route::post('/auth/login', 'login');
});

Route::middleware(['auth:sanctum', 'ability:member,manager,admin'])->controller(UserController::class)->group(function () {
    Route::get('/auth/account', 'account');
    Route::post('/auth/logout', 'logout');
    Route::post('/auth/update-account/{id}', 'updateAccount');
    Route::put('/auth/update-password', 'updatePassword');
    Route::delete('/auth/remove-account', 'removeAccount');
});

Route::middleware(['auth:sanctum', 'ability:admin'])->controller(AdminController::class)->group(function () {
    Route::get('/auth/admin/users', 'getAccounts');
    Route::get('/auth/admin/users/{id}', 'getSingleAccount');
    Route::put('/auth/admin/users/{id}/update-password', 'updatePassword');
    Route::put('/auth/admin/users/{id}/update-role', 'updateRole');
});

/****************************************************************************************
 *                   >>>>>>>     Category Modules     <<<<<<<
 ***************************************************************************************/
Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'getCategories');
    Route::get('/categories/{id}', 'getSingleCategory');
});

Route::middleware(['auth:sanctum', 'ability:manager,admin'])->controller(CategoryController::class)->group(function () {
    Route::post('/categories', 'createCategory');
    Route::put('/categories/{id}', 'updateCategory');
    Route::delete('/categories/{id}', 'deleteCategory');
});

/****************************************************************************************
 *                    >>>>>>>     Product Modules     <<<<<<<                           *
 ***************************************************************************************/
Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'getProducts');
    Route::get('/products/{id}', 'getSingleProduct');
});

Route::middleware(['auth:sanctum', 'ability:manager,admin'])->controller(ProductController::class)->group(function () {
    Route::post('/products', 'createProduct');
    Route::post('/products/{id}', 'updateProduct');
    Route::delete('/products/{id}', 'deleteProduct');
});

/****************************************************************************************
 *                        -----     Cart Modules     -----
 ***************************************************************************************/
Route::middleware(['auth:sanctum', 'ability:member,manager,admin'])->controller(CartController::class)->group(function () {
    Route::post('/cart/products/add', 'add');
    Route::post('/cart/products/increment', 'increaseProductsToCart');
    Route::post('/cart/products/decrement', 'decreaseProductsFromCart');
    Route::delete('/cart/{cId}/products/{pId}', 'destroyProducts');
    Route::get('/cart/products', 'getCart');
});

/****************************************************************************************
 *                        -----     Order Modules     -----
 ***************************************************************************************/
Route::middleware(['auth:sanctum', 'ability:member,manager,admin'])->controller(OrderController::class)->group(function () {
    Route::post('/orders', 'createOrder');
    Route::delete('/orders/{oId}', 'deleteOrder');
    Route::get('/orders/{oId}', 'getSingleOrder');
    Route::get('/orders', 'getOrders');
});

Route::middleware(['auth:sanctum', 'ability:manager,admin'])->controller(OrderController::class)->group(function () {
    Route::put('/orders/{oId}', 'updateOrder');
});
