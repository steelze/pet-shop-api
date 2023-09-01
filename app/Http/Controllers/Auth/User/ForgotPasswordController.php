<?php

namespace App\Http\Controllers\Auth\User;

use App\Helpers\RespondWith;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\User\ForgotPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class ForgotPasswordController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/user/forgot-password",
     *      operationId="forgotPassword",
     *      tags={"User"},
     *      summary="Creates a token to reset a user password",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"email"},
     *                  properties={
     *                      @OA\Property(
     *                          property="email",
     *                          type="string",
     *                          description="User email",
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
    public function __invoke(ForgotPasswordRequest $request): JsonResponse
    {
        $credentials = array_merge($request->only('email'), ['is_admin' => false]);

        $status = Password::sendResetLink($credentials, fn ($user, $token) => $token);

        return match ($status) {
            Password::INVALID_USER => RespondWith::error(__($status), code: Response::HTTP_NOT_FOUND),
            Password::RESET_THROTTLED => RespondWith::error(
                __(
                    $status,
                    ['seconds' => config('auth.passwords.users.throttle')]
                ),
                code: Response::HTTP_UNPROCESSABLE_ENTITY
            ),
            default => RespondWith::success(['reset_token' => $status]),
        };
    }
}
