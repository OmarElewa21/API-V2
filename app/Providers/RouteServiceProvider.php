<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        Route::bind('admin', function ($value) {
            return \App\Models\User::withTrashed()->whereUuid($value)->firstOrFail();
        });

        Route::bind('country_partner', function ($value) {
            $user = \App\Models\User::withTrashed()->whereUuid($value)->firstOrFail();
            if($user->hasRole('country partner')){
                return $user;
            }else{
                return abort(403, 'User Is Not A Country Partner');
            }
        });
        
        Route::bind('country_partner_assistant', function ($value) {
            $user = \App\Models\User::withTrashed()->whereUuid($value)->firstOrFail();
            if($user->hasRole('country partner assistant')){
                return $user;
            }else{
                return abort(403, 'User Is Not A Country Partner Assistant');
            }
        });

        Route::bind('school_manager', function ($value) {
            $user = \App\Models\User::withTrashed()->whereUuid($value)->firstOrFail();
            if($user->hasRole('school manager')){
                return $user;
            }else{
                return abort(403, 'User Is Not A School Manager');
            }
        });

        Route::bind('teacher', function ($value) {
            $user = \App\Models\User::withTrashed()->whereUuid($value)->firstOrFail();
            if($user->hasRole('teacher')){
                return $user;
            }else{
                return abort(403, 'User Is Not A Teacher');
            }
        });

        Route::bind('participant', function ($value) {
            return \App\Models\Participant::whereUuid($value)->firstOrFail();
        });

        Route::bind('role', function ($value) {
            return \App\Models\Role::whereUuid($value)->firstOrFail();
        });

        Route::bind('organization', function ($value) {
            return \App\Models\Organization::whereUuid($value)->firstOrFail();
        });

        Route::bind('school', function ($value) {
            return \App\Models\School::withTrashed()->whereUuid($value)->firstOrFail();
        });

        Route::bind('domain', function ($value) {
            return \App\Models\DomainsTags::whereUuid($value)->firstOrFail();
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
