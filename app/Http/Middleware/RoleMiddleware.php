<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        //check if the user is logged in
        if(!Auth::check()){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        //Allow access based on roles
        if(in_array(Auth::user()->role, $roles)){
            return $next($request);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'You do not have permission to access this resource'
        ], 403);
        
    }
}
