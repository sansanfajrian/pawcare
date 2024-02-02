<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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
        $firstRole = $roles->first();
        $firstRoleId = $firstRole ? (string) $firstRole['_id'] : null;
        if (Auth::check() && Auth::user()->role->id == $firstRoleId)
        {
            $this->redirectTo = route('admin.dashboard');
        } else {
            $this->redirectTo = route('author.dashboard');
        }
        $this->middleware('guest');
    }
}
