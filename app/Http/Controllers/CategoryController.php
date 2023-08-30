<?php

namespace App\Http\Controllers;

use App\Helpers\RespondWith;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function listAll(Request $request): JsonResponse
    {
        return RespondWith::raw([]);
    }

    public function findOne(string $uuid): JsonResponse
    {
        return RespondWith::raw([]);
    }

    public function create(CategoryRequest $request): JsonResponse
    {
        return RespondWith::success();
    }

    public function edit(CategoryRequest $request, string $uuid): JsonResponse
    {
        return RespondWith::success();
    }

    public function delete(string $uuid): JsonResponse
    {
        return RespondWith::success();
    }
}
