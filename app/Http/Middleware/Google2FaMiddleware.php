<?php

namespace App\Http\Middleware;

use App\Exceptions\Middleware\Google2Fa\{
    Empty2Fa,
    Incorrect2Fa,
    NotEnable2Fa,
};
use App\Http\Facades\ResponseFacade;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FAQRCode\Google2FA;
use Symfony\Component\HttpFoundation\Response;

class Google2FaMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if(!Auth::user()->enable_2_fact){
            return ResponseFacade::makeBadResponse(new NotEnable2Fa());
        }
        if(empty($request['twoFaCod'])){
            return ResponseFacade::makeBadResponse(new Empty2Fa());
        }
        $google2fa = new Google2FA();
        $google2fa_secret = Auth::user()->google2fa_secret;
        if(!$google2fa->verifyKey(decrypt($google2fa_secret),$request['twoFaCod'])){
            return ResponseFacade::makeBadResponse(new Incorrect2Fa());
        }
        return $next($request);
    }
}
