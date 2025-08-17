<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next , ...$role): Response
    {
        if(Auth::check()){
            $roleName = Auth::user()->role;
            $hasAccess = in_array($roleName,$role);
            if(!$hasAccess){
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }
        return $next($request);
    }
}
