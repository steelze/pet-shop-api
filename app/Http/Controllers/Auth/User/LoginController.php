<?php

namespace App\Http\Controllers\Auth\User;

use App\Helpers\RespondWith;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\User\LoginRequest;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        return RespondWith::success();
    }
}
