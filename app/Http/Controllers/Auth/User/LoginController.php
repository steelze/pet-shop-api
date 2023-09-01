<?php

namespace App\Http\Controllers\Auth\User;

use App\Helpers\RespondWith;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/user/login",
     *      operationId="userLogin",
     *      tags={"User"},
     *      summary="Login an User account",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"email", "password"},
     *                  properties={
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
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $credentials = array_merge($request->only(['email', 'password']), ['is_admin' => false]);

        $token = auth()->attempt($credentials);

        if (!$token) {
            return RespondWith::error('Failed to authenticate user', code: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return RespondWith::success(['token' => $token->toString()]);
    }
}
