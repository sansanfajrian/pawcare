<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

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
        if (Auth::check() && Auth::user()->role->id == 2) 
        {
            $isApproved = Auth::user()->userDoctorDetails()->first()->is_approved ?? 0;
            if ($isApproved < 1) {
                Auth::logout();
                return redirect()->route('login')
                    ->withErrors([
                        'msg' => "Your account hasn't been approved. Please wait for the admin to approve your account."
                    ]);
            }
            return $next($request);
        } else {
            return redirect()->route('login');
        }
    }
}
