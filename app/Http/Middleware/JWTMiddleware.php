<?php

namespace App\Http\Middleware;

use App\Exceptions\JWTException;
use App\Services\JWTService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $jwt = new JWTService;

        $token = $jwt->getTokenFromRequestHeader($request);

        if (!$token)  {
            throw new UnauthorizedHttpException('jwt-auth', 'Unauthorized');
        }

        try {
            $jwt->parseToken($token);
        } catch (JWTException $e) {
            throw new UnauthorizedHttpException('jwt-auth', 'Unauthorized', $e->getPrevious(), $e->getCode());
        }

        return $next($request);
    }
}
