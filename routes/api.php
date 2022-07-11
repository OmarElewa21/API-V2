<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\TasksController;

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
            'roles'                     => App\Http\Controllers\RoleController::class,
            'domains'                   => App\Http\Controllers\DomainsTagsController::class,
            'difficulty_groups'         => App\Http\Controllers\DifficultyGroupController::class,
            'collections'               => App\Http\Controllers\CollectionController::class
        ]);
        
        Route::delete('roles/action/mass_delete', [App\Http\Controllers\RoleController::class, "massDelete"]);

        Route::post('users/action/mass_enable', [App\Http\Controllers\UsersController::class, 'mass_enable']);
        Route::post('users/action/mass_disable', [App\Http\Controllers\UsersController::class, 'mass_disable']);
        Route::delete('users/action/mass_delete', [App\Http\Controllers\UsersController::class, 'mass_delete']);

        Route::delete('schools/action/mass_delete', [App\Http\Controllers\SchoolController::class, "massDelete"]);
        Route::post('schools/action/reject/{school}', [App\Http\Controllers\SchoolController::class, "reject"]);
        Route::post('schools/action/mass_approve', [App\Http\Controllers\SchoolController::class, "massApprove"]);

        Route::delete('organizations/action/mass_delete', [App\Http\Controllers\OrganizationController::class, "massDelete"]);

        Route::put('domains/topic/{topic}', [App\Http\Controllers\DomainsTagsController::class, 'update_topic']);
        Route::post('domains/action/mass_approve', [App\Http\Controllers\DomainsTagsController::class, "massApprove"]);
        Route::delete('domains/action/mass_delete', [App\Http\Controllers\DomainsTagsController::class, "massDelete"]);

        Route::delete('difficulty_groups/action/mass_delete', [App\Http\Controllers\DifficultyGroupController::class, "massDelete"]);

        Route::apiResource('tasks', TasksController::class)->except('update');
        Route::group(['prefix'=>'tasks','as'=>'tasks.'], function(){
            Route::put('/updateTask/{task}', [TasksController::class, 'updateTask'])->name('updateTask');
            Route::put('/updateTaskContent/{task}', [TasksController::class, 'updateTaskContent'])->name('updateTaskContent');
            Route::put('/updateRecommendations/{task}', [TasksController::class, 'updateRecommendations'])->name('updateRecommendations');
            Route::put('/updateAnswers/{task}', [TasksController::class, 'updateAnswers'])->name('updateAnswers');
        });
        Route::delete('tasks/action/mass_delete', [TasksController::class, "massDelete"]);

        Route::post('collections/action/mass_approve', [App\Http\Controllers\CollectionController::class, "massApprove"]);
        Route::delete('collections/action/mass_delete', [App\Http\Controllers\CollectionController::class, "massDelete"]);
    });

    Route::middleware('role:super admin|admin|country partner')
        ->apiResource('country_partner.country_partner_assistants', App\Http\Controllers\User\CountryPartnerAssistantController::class)->except('index')->shallow();

    Route::group(['middleware' => 'role:super admin|admin|country partner|country partner assistant'], function() {
        Route::apiResource('school_managers', App\Http\Controllers\User\SchoolManagerController::class)->except('index');
        Route::apiResource('schools', App\Http\Controllers\SchoolController::class);
    });

    Route::group(['middleware' => 'role:school manager|teacher'], function() {
        Route::get('school/showRelated', [App\Http\Controllers\SchoolController::class, 'showRelated']);
        Route::put('school/updateRelated', [App\Http\Controllers\SchoolController::class, 'updateRelated']);
    });

    Route::middleware('role:super admin|admin|country partner|country partner assistant|school manager')
        ->apiResource('teachers', App\Http\Controllers\User\TeacherController::class)->except('index');

    Route::group(['middleware' => 'role:super admin|admin|country partner|country partner assistant|school manager|teacher'], function() {
        Route::apiResource('participants', App\Http\Controllers\User\ParticipantController::class);
    });
});
