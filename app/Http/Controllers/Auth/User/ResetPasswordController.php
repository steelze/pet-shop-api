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
    /**
     * @OA\Post(
     *      path="/api/v1/user/reset-password-token",
     *      operationId="resetPassword",
     *      tags={"User"},
     *      summary="Reset a password with the token",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"token", "email", "password", "password_confirmation"},
     *                  properties={
     *                      @OA\Property(
     *                          property="first_name",
     *                          description="User firstname",
     *                          type="string",
     *                      ),
     *                      @OA\Property(
     *                          property="token",
     *                          description="User reset token",
     *                          type="string",
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string",
     *                          description="User email",
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string",
     *                          description="User password",
     *                      ),
     *                      @OA\Property(
     *                          property="password_confirmation",
     *                          type="string",
     *                          description="User password",
     *                      ),
     *                  },
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      ),
     *  )
     */
    public function __invoke(ResetPasswordRequest $request): JsonResponse
    {
        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $credentials = array_merge($request->only('email', 'password', 'token'), ['is_admin' => false]);

        $status = Password::reset($credentials, function ($user) use ($request): void {
            $user->forceFill(['password' => Hash::make($request->password)])->save();

            event(new PasswordReset($user));
        });

        return match ($status) {
            Password::PASSWORD_RESET => RespondWith::success(['message' => __($status)]),
            default => RespondWith::error(__(Password::INVALID_TOKEN), code: Response::HTTP_UNPROCESSABLE_ENTITY),
        };
    }
}
