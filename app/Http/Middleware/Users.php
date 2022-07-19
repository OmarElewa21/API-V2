<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Users
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
        $user = $request->route()->parameter($request->route()->parameterNames()[0]);
        if(auth()->user()->allowedForRoute($user, $request->route()->parameterNames()[0])){
            return $next($request);
        }else{
            return response()->json(['message' => 'Not allowed for this request'], 422);
        }
    }
}
