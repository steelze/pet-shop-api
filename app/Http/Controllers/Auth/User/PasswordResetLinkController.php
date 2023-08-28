<?php

namespace App\Http\Controllers\Auth\User;

use App\Helpers\RespondWith;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\User\PasswordResetLinkRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetLinkController extends Controller
{
    public function __invoke(PasswordResetLinkRequest $request): JsonResponse
    {
        $credentials = array_merge($request->only('email'), ['is_admin' => false]);

        $status = Password::sendResetLink($credentials, fn($user, $token) => $token);

       return match ($status) {
            Password::INVALID_USER => RespondWith::error(__($status), code: Response::HTTP_NOT_FOUND),
            Password::RESET_THROTTLED => RespondWith::error(__($status, ['seconds' => config('auth.passwords.users.throttle')]), code: Response::HTTP_UNPROCESSABLE_ENTITY),
            default => RespondWith::success(['reset_token' => $status]),
        };
    }
}
