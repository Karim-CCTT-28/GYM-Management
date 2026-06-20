<?php

namespace App\Http\Middleware;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        $id = session('user_id');

        $user = User::select('role')->where('id', $id)->firstOrFail();
        $role = $user->role;
        // return response()->json($role);
        if ($role == 'A') {
            return $next($request);
        }
        return response()->view('Denied');
    }
}
