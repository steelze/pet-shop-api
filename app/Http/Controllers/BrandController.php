<?php

namespace App\Http\Controllers;

use App\Helpers\RespondWith;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function listAll(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 20;

        $allowedSortBy = ['title'];
        $sortBy = in_array($request->sortBy, $allowedSortBy) ? $request->sortBy : null;
        $sortByDirection = $request->boolean('desc', false) ? 'desc' : 'asc';

        $categories = Brand::when($request->title, fn($sql, $value) => $sql->where('title', 'like', "%$value%"))
            ->when(!empty($sortBy), fn($sql) => $sql->orderBy($sortBy, $sortByDirection))
            ->paginate($limit);

        return RespondWith::raw($categories);
    }

    public function findOne(string $uuid): JsonResponse
    {
        $category = Brand::where('uuid', $uuid)->firstOrFail();
        return RespondWith::success($category->toArray());
    }

    public function create(BrandRequest $request): JsonResponse
    {
        $slug = Str::slug($request->title);
        $category = Brand::create(['title' => $request->title, 'slug' => $slug]);

        return RespondWith::success($category->toArray());
    }

    public function update(BrandRequest $request, string $uuid): JsonResponse
    {
        $category = Brand::where('uuid', $uuid)->firstOrFail();

        $slug = Str::slug($request->title);
        $category->update(['title' => $request->title, 'slug' => $slug]);

        return RespondWith::success($category->toArray());
    }

    public function delete(string $uuid): JsonResponse
    {
        $category = Brand::where('uuid', $uuid)->firstOrFail();
        $category->delete();

        return RespondWith::success();
    }
}