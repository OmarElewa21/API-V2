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
Route::post('send_reset_link', [UsersController::class,"sendResetLink"]);
Route::post('change_password', [UsersController::class,"changePassword"]);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post('logout', [UsersController::class,"logout"]);
    Route::resource('countries', App\Http\Controllers\CountryController::class)->only('index', 'show');

    Route::group(['middleware' => ['role:super admin']], function() {
        Route::get('users', [App\Http\Controllers\UsersController::class, 'index'])->name('users.index');
        Route::apiResource('admins', App\Http\Controllers\User\AdminsController::class)->except('index');
        
        Route::apiResource('roles', App\Http\Controllers\RoleController::class);
        Route::delete('roles/action/mass_delete', [App\Http\Controllers\RoleController::class, "massDelete"]);
    });

    Route::group(['middleware' => ['role:super admin|admin']], function() {
        Route::apiResource('admin/users', App\Http\Controllers\UsersController::class);
        Route::apiResources([
            'organizations'             => App\Http\Controllers\OrganizationController::class,
            'users/country_partners'    => App\Http\Controllers\User\CountryPartnerController::class,
        ]);
        Route::delete('schools/action/mass_delete', [App\Http\Controllers\SchoolController::class, "massDelete"]);
        Route::post('schools/action/reject/{school}', [App\Http\Controllers\SchoolController::class, "reject"]);
        Route::post('schools/action/mass_approve', [App\Http\Controllers\SchoolController::class, "massApprove"]);

        Route::delete('organizations/action/mass_delete', [App\Http\Controllers\OrganizationController::class, "massDelete"]);
    });

    Route::middleware('role:super admin|admin|country partner')
        ->apiResource('users/country_partner.country_partner_assistants', App\Http\Controllers\User\CountryPartnerAssistantController::class)->shallow();

    Route::group(['middleware' => 'role:super admin|admin|country partner|country partner assistant'], function() {
        Route::apiResource('users/school_managers', App\Http\Controllers\User\SchoolManagerController::class);
        Route::middleware('SchoolRouteEligibilty')->apiResource('schools', App\Http\Controllers\SchoolController::class);
    });

    Route::middleware('role:super admin|admin|country partner|country partner assistant|school manager')
        ->apiResource('users/teachers', App\Http\Controllers\User\TeacherController::class);

    Route::group(['middleware' => 'role:super admin|admin|country partner|country partner assistant|school manager|teacher'], function() {
        Route::apiResource('participants', App\Http\Controllers\User\ParticipantController::class);
    });

    Route::middleware(['role:school manager|teacher', 'SchoolRouteEligibilty'])
        ->resource('schools', App\Http\Controllers\SchoolController::class)->only('update', 'show');

});
