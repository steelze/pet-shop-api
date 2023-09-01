<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Helpers\RespondWith;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Admin\CreateAdminRequest;
use App\Repositories\UserRepository;
use App\Services\JWTService;
use Illuminate\Http\JsonResponse;

class CreateAdminController extends Controller
{
    public function __invoke(CreateAdminRequest $request, JWTService $service, UserRepository $repository): JsonResponse
    {
        $payload = array_merge($request->validated(), ['is_marketing' => $request->boolean('is_marketing')]);

        $user = $repository->create($payload);

        $user->forceFill(['is_admin' => true])->save();

        $token = $service->fromUser($user);

        $data = array_merge($user->toArray(), ['token' => $token->toString()]);

        return RespondWith::success($data);
    }
}
