<?php

namespace App\Http\Controllers;

use App\Helpers\RespondWith;
use App\Http\Requests\EditProfileRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function edit(EditProfileRequest $request, UserRepository $repository): JsonResponse
    {
        $payload = array_merge($request->validated(), ['is_marketing' => $request->boolean('is_marketing')]);
        $user = $repository->update(auth()->user(), $payload);

        return RespondWith::success($user->toArray());
    }
}
