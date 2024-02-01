<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminMiddleware
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
        $firstRole = $roles->first();
        $firstRoleId = $firstRole ? (string) $firstRole['_id'] : null;
        if (Auth::check() && Auth::user()->role->id == $firstRoleId)
        {
            return $next($request);
        } else {
            return redirect()->route('login');
        }
    }
}
