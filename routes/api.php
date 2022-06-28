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

    Route::middleware('role:super admin|admin|country partner|country partner assistant|school manager')
        ->get('users', [App\Http\Controllers\UsersController::class, 'index'])->name('users.index');

    Route::group(['middleware' => ['role:super admin']], function() {
        Route::apiResource('admins', App\Http\Controllers\User\AdminsController::class)->except('index');
    });

    Route::group(['middleware' => ['role:super admin|admin']], function() {
        Route::apiResource('country_partners', App\Http\Controllers\User\CountryPartnerController::class)->except('index');
        Route::apiResources([
            'organizations'             => App\Http\Controllers\OrganizationController::class,
            'roles'                     => App\Http\Controllers\RoleController::class
        ]);

        Route::delete('roles/action/mass_delete', [App\Http\Controllers\RoleController::class, "massDelete"]);

        Route::delete('schools/action/mass_delete', [App\Http\Controllers\SchoolController::class, "massDelete"]);
        Route::post('schools/action/reject/{school}', [App\Http\Controllers\SchoolController::class, "reject"]);
        Route::post('schools/action/mass_approve', [App\Http\Controllers\SchoolController::class, "massApprove"]);

        Route::delete('organizations/action/mass_delete', [App\Http\Controllers\OrganizationController::class, "massDelete"]);
    });

    Route::middleware('role:super admin|admin|country partner')
        ->apiResource('country_partner.country_partner_assistants', App\Http\Controllers\User\CountryPartnerAssistantController::class)->except('index')->shallow();

    Route::group(['middleware' => 'role:super admin|admin|country partner|country partner assistant'], function() {
        Route::apiResource('school_managers', App\Http\Controllers\User\SchoolManagerController::class)->except('index');
        Route::apiResource('schools', App\Http\Controllers\SchoolController::class);
    });

    Route::middleware('role:super admin|admin|country partner|country partner assistant|school manager')
        ->apiResource('users/teachers', App\Http\Controllers\User\TeacherController::class);

    Route::group(['middleware' => 'role:super admin|admin|country partner|country partner assistant|school manager|teacher'], function() {
        Route::apiResource('participants', App\Http\Controllers\User\ParticipantController::class);
    });
});
