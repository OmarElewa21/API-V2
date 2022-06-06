<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        if($request->user()->hasOwnPermissionSet()){
            if($request->user()->checkRouteEligibility($request->route()->getName())){
                return $next($request);
            }else{
                return response()->json(['message' => 'User is not authorized for this request'], 401);
            }
        }

        $roles = explode("|", $roles);
        foreach($roles as $role){
            if ( !$request->user()->hasRole($role) ) {
                return response()->json(['message' => 'User is not authorized for this request'], 401);
            }
            return $next($request);
        }
    }
}
