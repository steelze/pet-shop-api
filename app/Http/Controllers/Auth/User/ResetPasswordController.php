<?php

namespace App\Http\Controllers\Auth\User;

use App\Helpers\RespondWith;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\User\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends Controller
{
    public function __invoke(ResetPasswordRequest $request): JsonResponse
    {
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $credentials = array_merge($request->only('email', 'password', 'token'), ['is_admin' => false]);

        $status = Password::reset($credentials, function ($user) use ($request) {
            $user->forceFill(['password' => Hash::make($request->password)])->save();

            event(new PasswordReset($user));
        });

        return match ($status) {
            Password::PASSWORD_RESET => RespondWith::success(['message' => __($status)]),
            default => RespondWith::error(__(Password::INVALID_TOKEN), code: Response::HTTP_UNPROCESSABLE_ENTITY),
        };
    }
}
