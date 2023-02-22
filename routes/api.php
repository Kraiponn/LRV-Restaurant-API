<?php

use App\Http\Controllers\API\Auth\AdminController;
use App\Http\Controllers\API\Auth\UserController;
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
