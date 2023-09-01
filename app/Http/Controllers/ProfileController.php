<?php

namespace App\Http\Controllers;

use App\Helpers\RespondWith;
use App\Http\Requests\EditProfileRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/user/",
     *      operationId="viewAUser",
     *      tags={"User"},
     *      summary="View a User account",
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      ),
     *   )
     */
    public function view(Request $request): JsonResponse
    {
        $payload = array_merge($request->validated(), ['is_marketing' => $request->boolean('is_marketing')]);
        $user = $repository->update(auth()->user(), $payload);

        return RespondWith::success($user->toArray());
    }

    /**
     * @OA\Put(
     *      path="/api/v1/user/edit",
     *      operationId="updateMyAccount",
     *      tags={"User"},
     *      summary="Update a User account",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"first_name", "last_name", "email", "password", "password_confirmation", "address", "phone_number"},
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
    public function edit(EditProfileRequest $request, UserRepository $repository): JsonResponse
    {
        $payload = array_merge($request->validated(), ['is_marketing' => $request->boolean('is_marketing')]);
        $user = $repository->update(auth()->user(), $payload);

        return RespondWith::success($user->toArray());
    }
}
