<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
  public function handle(Request $request, Closure $next): Response
{
    // if (!session()->has('user_name')) {
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Unauthenticated'
    //     ], 401);
    // }

    return $next($request);
}
}
