<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\ClientRepository;
use Lcobucci\JWT\Parser;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected static function getCurrentToken($request)
	{
	    $tokenRepository = new TokenRepository();
	    $jwt = (new Parser())->parse($request->bearerToken());
	    $token = $tokenRepository->find($jwt->getClaim('jti'));

	    return $token;
	}
    
    public function index()
    {
        $sliders = Slider::all();
        $items = Item::all();
        $categories = Category::all();
        return view('frontend.view', compact('sliders','items','categories'));
    }
}
