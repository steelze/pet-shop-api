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
    public function delete(User $user, UserRepository $repository): JsonResponse
    {
        abort_if($user->is_admin, Response::HTTP_NOT_FOUND, 'User not found');
        $repository->delete($user);

        return RespondWith::success();
    }

    public function edit(EditProfileRequest $request, User $user, UserRepository $repository): JsonResponse
    {
        abort_if($user->is_admin, Response::HTTP_NOT_FOUND, 'User not found');

        $payload = array_merge($request->validated(), ['is_marketing' => $request->boolean('is_marketing')]);
        $user = $repository->update($user, $payload);

        return RespondWith::success($user->toArray());
    }

    public function listing(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 20;

        $allowedSortBy = ['first_name', 'email', 'created_at'];
        $sortBy = in_array($request->sortBy, $allowedSortBy) ? $request->sortBy : null;
        $sortByDirection = $request->boolean('desc', false) ? 'desc' : 'asc';

        $filterByMarketing = in_array($request->marketing, [1, 0, '1', '0'], true);

        $users = User::users()
            ->when($request->first_name, fn($sql, $value) => $sql->where('first_name', 'like', "%$value%"))
            ->when($request->email, fn($sql, $value) => $sql->where('email', 'like', "%$value%"))
            ->when($request->phone, fn($sql, $value) => $sql->where('phone_number', 'like', "%$value%"))
            ->when($request->address, fn($sql, $value) => $sql->where('address', 'like', "%$value%"))
            ->when($request->created_at, fn($sql, $value) => $sql->where('created_at', 'like', "%$value%"))
            ->when($filterByMarketing, fn($sql) => $sql->where('is_marketing', $request->marketing))
            ->when(!empty($sortBy), fn($sql) => $sql->orderBy($sortBy, $sortByDirection))
            ->paginate($limit);

        return RespondWith::raw($users);
    }
}
