<?php

namespace App\Http\Controllers\Auth\User;

use App\Helpers\RespondWith;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\User\CreateUserRequest;
use App\Repositories\UserRepository;
use App\Services\JWTService;
use Illuminate\Http\JsonResponse;

class CreateUserController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/user/create",
     *      operationId="createUserAccount",
     *      tags={"User"},
     *      summary="Create a User account",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "first_name", "last_name", "email", "password",
     *                      "password_confirmation", "address", "phone_number"
     *                  },
     *                  properties={
     *                      @OA\Property(
     *                          property="first_name",
     *                          description="User firstname",
     *                          type="string",
     *                      ),
     *                      @OA\Property(
     *                          property="last_name",
     *                          description="User lastname",
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
     *                      @OA\Property(
     *                          property="avatar",
     *                          type="string",
     *                          description="Avatar image UUID",
     *                      ),
     *                      @OA\Property(
     *                          property="address",
     *                          type="string",
     *                          description="User main address",
     *                      ),
     *                      @OA\Property(
     *                          property="phone_number",
     *                          type="string",
     *                          description="User main phone number",
     *                      ),
     *                      @OA\Property(
     *                          property="is_marketing",
     *                          type="string",
     *                          description="User marketing preferences",
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
    public function __invoke(CreateUserRequest $request, JWTService $service, UserRepository $repository): JsonResponse
    {
        $payload = array_merge(
            $request->validated(),
            ['is_admin' => false, 'is_marketing' => $request->boolean('is_marketing')]
        );

        $user = $repository->create($payload);

        $token = $service->fromUser($user);

        $user->token = $token->toString();

        $data = array_merge($user->toArray(), ['token' => $token->toString()]);

        return RespondWith::success($data);
    }
}
