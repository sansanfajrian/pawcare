<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $roles = DB::collection('roles')->get();
        
        $firstRole = $roles->first();
        $secondRole = optional($roles->get(1));

        $firstRoleId = $firstRole ? (string) $firstRole['_id'] : null;
        $secondRoleId = $secondRole ? (string) $secondRole['_id'] : null;
        if (Auth::check() && Auth::user()->role->id == $firstRoleId){
            $this->redirectTo = route('admin.dashboard');
        } else if(Auth::check() && Auth::user()->role->id == $secondRoleId){
            $this->redirectTo = route('author.dashboard');
        }
        $this->middleware('guest')->except('logout');
    }
}
