<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $role = (auth()->user()->role == 'admin' || auth()->user()->role == 'Admin' ||  auth()->user()->role == 'editor' ||  auth()->user()->role == 'Editor');
            if(auth()->check() && $role ){
            return $next($request);
           }else{
             return response()->json(['status'=>403, 'error'=>'unauthorized'], 403);
           }

        // return $next($request);
    }
}
