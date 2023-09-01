<?php

namespace App\Http\Controllers;

use App\Helpers\RespondWith;
use App\Http\Requests\EditProfileRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/admin/user-listing",
     *      operationId="listAllUsers",
     *      tags={"Admin"},
     *      summary="List all users",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *         style="form"
     *      ),
     *      @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *         style="form"
     *      ),
     *      @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         @OA\Schema(
     *             type="sring",
     *         ),
     *         style="form"
     *      ),
     *      @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         @OA\Schema(
     *             type="boolean",
     *         ),
     *         style="form"
     *      ),
     *      @OA\Parameter(
     *         name="fist_name",
     *         in="query",
     *         @OA\Schema(
     *             type="sring",
     *         ),
     *         style="form"
     *      ),
     *      @OA\Parameter(
     *         name="email",
     *         in="query",
     *         @OA\Schema(
     *             type="sring",
     *         ),
     *         style="form"
     *      ),
     *      @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         @OA\Schema(
     *             type="sring",
     *         ),
     *         style="form"
     *      ),
     *      @OA\Parameter(
     *         name="address",
     *         in="query",
     *         @OA\Schema(
     *             type="sring",
     *         ),
     *         style="form"
     *      ),
     *      @OA\Parameter(
     *         name="created_at",
     *         in="query",
     *         @OA\Schema(
     *             type="sring",
     *         ),
     *         style="form"
     *      ),
     *      @OA\Parameter(
     *         name="marketing",
     *         in="query",
     *         @OA\Schema(
     *             type="boolean",
     *         ),
     *         style="form"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      ),
     *   )
     */
    public function listing(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 20;

        $allowedSortBy = ['first_name', 'email', 'created_at'];
        $sortBy = in_array($request->sortBy, $allowedSortBy) ? $request->sortBy : null;
        $sortByDirection = $request->boolean('desc', false) ? 'desc' : 'asc';

        $filterByMarketing = in_array($request->marketing, [1, 0, '1', '0'], true);

        $users = User::users()
            ->when($request->first_name, fn ($sql, $value) => $sql->where('first_name', 'like', "%{$value}%"))
            ->when($request->email, fn ($sql, $value) => $sql->where('email', 'like', "%{$value}%"))
            ->when($request->phone, fn ($sql, $value) => $sql->where('phone_number', 'like', "%{$value}%"))
            ->when($request->address, fn ($sql, $value) => $sql->where('address', 'like', "%{$value}%"))
            ->when($request->created_at, fn ($sql, $value) => $sql->where('created_at', 'like', "%{$value}%"))
            ->when($filterByMarketing, fn ($sql) => $sql->where('is_marketing', $request->marketing))
            ->when(!empty($sortBy), fn ($sql) => $sql->orderBy($sortBy, $sortByDirection))
            ->paginate($limit);

        return RespondWith::raw($users);
    }

    /**
     * @OA\Put(
     *      path="/api/v1/admin/user-edit/{uuid}",
     *      operationId="updateUserAccount",
     *      tags={"Admin"},
     *      summary="Edit a User account",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         @OA\Schema(type="string"),
     *         style="form",
     *         required=true
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  required={
     *                      "first_name", "last_name", "email",
     *                          "password", "password_confirmation", "address", "phone_number"
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
    public function edit(EditProfileRequest $request, string $uuid, UserRepository $repository): JsonResponse
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        abort_if($user->is_admin, Response::HTTP_NOT_FOUND, 'User not found');

        $payload = array_merge($request->validated(), ['is_marketing' => $request->boolean('is_marketing')]);
        $user = $repository->update($user, $payload);

        return RespondWith::success($user->toArray());
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/user-delete/{uuid}",
     *      operationId="deleteeUserAccount",
     *      tags={"Admin"},
     *      summary="Delete a User account",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         @OA\Schema(type="string"),
     *         style="form",
     *         required=true
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
     *          response=500,
     *          description="Internal server error",
     *      ),
     *  )
     */
    public function delete(string $uuid, UserRepository $repository): JsonResponse
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        abort_if($user->is_admin, Response::HTTP_NOT_FOUND, 'User not found');
        $repository->delete($user);

        return RespondWith::success();
    }
}
