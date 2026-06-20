<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
class Employee
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $id = session('user_id');

        if(!$id) return response()->view('Denied');


        $user = User::select('role')->where('id', $id)->firstOrFail();
        $role = $user->role;
        // return response()->json($role);
        if ($role == 'E' || $role == 'A') {
            return $next($request);
        }
        return response()->view('Denied');
    }
}
