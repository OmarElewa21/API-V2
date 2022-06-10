<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\User\AdminsController;
use App\Http\Controllers\User\CountryPartnerController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\SchoolController;


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
Route::post('send_reset_link', [UsersController::class,"sendResetLink"]);
Route::post('change_password', [UsersController::class,"changePassword"]);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post('logout', [UsersController::class,"logout"]);
    Route::resource('countries', CountryController::class)->only('index', 'show');

    Route::group(['middleware' => ['role:super admin']], function() {
        Route::apiResource('users/admins', AdminsController::class);
        Route::apiResource('roles', RoleController::class);
        Route::delete('roles/action/mass_delete', [RoleController::class, "massDelete"]);
    });

    Route::group(['middleware' => ['role:super admin|admin']], function() {
        Route::apiResource('organizations', OrganizationController::class);
        Route::apiResource('users/country_partners', CountryPartnerController::class);
        Route::apiResource('schools', SchoolController::class);
        Route::delete('schools/action/mass_delete', [SchoolController::class, "massDelete"]);
    });
});
