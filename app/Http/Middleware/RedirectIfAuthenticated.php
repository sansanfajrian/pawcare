<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = Auth::guard($guard)->user();
        $roles = DB::collection('roles')->get();
        
        $firstRole = $roles->first();
        $secondRole = optional($roles->get(1));

        $firstRoleId = $firstRole ? (string) $firstRole['_id'] : null;
        $secondRoleId = $secondRole ? (string) $secondRole['_id'] : null;
        
        if ($user && $user->role) {
            if ($user->role->id == $firstRoleId) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role->id == $secondRoleId) {
                return redirect()->route('author.dashboard');
            }
        }
        return $next($request);
    }
}
