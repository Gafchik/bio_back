<?php

namespace App\Http\Middleware;

use App\Exceptions\BaseExceptions\Core\UnauthorizedJWTException;
use App\Http\Facades\ResponseFacade;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    public function handle(Request $request, Closure $next): JsonResponse | Response
    {
        $email = Auth::user()?->email;
        if(empty($email)){
            return ResponseFacade::makeBadResponse(new UnauthorizedJWTException());
        }
        $request['currentEmail'] = $email;
        return $next($request);
    }
}
