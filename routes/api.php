<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::post('login', [UsersController::class,"login"]);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::resource('users', UsersController::class);
    Route::post('logout', [UsersController::class,"logout"]);
    // Todo: may add future middleware to this resource route
    Route::resource('roles', RoleController::class)->except(['create', 'show']);
});
