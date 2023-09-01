<?php

namespace App\Http\Controllers\Auth\User;

use App\Helpers\RespondWith;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $credentials = array_merge($request->only(['email', 'password']), ['is_admin' => false]);

        $token = auth()->attempt($credentials);

        if (!$token) {
            return RespondWith::error('Failed to authenticate user', code: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return RespondWith::success(['token'=> $token->toString()]);
    }
}
