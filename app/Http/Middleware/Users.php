<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Participant;

class Users
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $routeParameter)
    {
        if(in_array(Str::after($request->route()->getName(), '.'), ['update', 'show', 'destroy'])){
            $user = $request->route()->parameter($routeParameter);
            
            if($routeParameter === 'participant'){
                if(! Participant::allowedForRoute($user)){
                    return response()->json(['message' => 'Not allowed for this request'], 422);
                }
            }else{
                if(!auth()->user()->allowedForRoute($user, $routeParameter)){
                    return response()->json(['message' => 'Not allowed for this request'], 422);
                }
            }
        }
        return $next($request);
    }
}
