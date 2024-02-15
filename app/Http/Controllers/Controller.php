<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\ClientRepository;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token\Builder;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected static function getCurrentToken($request)
	{
	    $tokenRepository = new TokenRepository();
	    $token = app(Parser::class)->parse($request->bearerToken())->claims()->get('jti');

	    return $token;
	}
    
    public function index()
    {
        return view('frontend.view');
    }
}
