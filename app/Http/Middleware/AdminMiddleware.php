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
      $editor = (auth()->user()->role == "Editor" || auth()->user()->role  == 'editor');
           if(auth()->check() && auth()->user()->role == 'admin' &&  $editor){
            return $next($request);
           }else{
             return response()->json(['status'=>403, 'error'=>'unauthorized'], 403);
           }

        // return $next($request);
    }
}
