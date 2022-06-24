<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SchoolRouteEligibilty
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
        $error = response()->json(['message' => 'Not authorized for operations on this school'], 401);
        switch ($request->route()->getName()) {
            case 'schools.show':
                if(!auth()->user()->checkShowEligibility()){
                    return $error;
                }
                break;
            case 'schools.update':
                if(!auth()->user()->checkUpdateEligibility()){
                    return $error;
                }
                break;
            default:
                break;
        }
        return $next($request);
    }
}
