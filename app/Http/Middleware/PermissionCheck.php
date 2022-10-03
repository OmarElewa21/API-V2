<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\CheckPermissionService as CPS;

class PermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth()->check() && is_string($request->route()->getName()) && array_key_exists($request->route()->getName(), CPS::ROUTE_CHECK_LIST)){
            if(CPS::checkAccessPermission($request->user(), $request->route()->getName())){
                return $next($request);
            }else{
                return response('User is not allowed for this action' ,403);
            }
        }

        return $next($request);
    }
}
