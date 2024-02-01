<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $roles = DB::collection('roles')->get();
        $secondRole = optional($roles->get(1));
        $secondRoleId = $secondRole ? (string) $secondRole['_id'] : null;
        if (Auth::check() && Auth::user()->role->id == $secondRoleId) 
        {
            return $next($request);
        } else {
            return redirect()->route('login');
        }
    }
}
