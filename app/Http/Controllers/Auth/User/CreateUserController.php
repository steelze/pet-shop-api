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
    public function __invoke(CreateUserRequest $request, JWTService $service, UserRepository $repository): JsonResponse
    {
        $payload = array_merge($request->validated(), ['is_admin' => false, 'is_marketing' => $request->boolean('is_marketing')]);

        $user = $repository->create($payload);

        $token = $service->fromUser($user);

        $user->token = $token->toString();

        return RespondWith::success($user->toArray());
    }
}
