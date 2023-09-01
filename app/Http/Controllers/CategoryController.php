<?php

namespace App\Http\Controllers;

use App\Helpers\RespondWith;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/catergories",
     *      operationId="getAllCategories",
     *      tags={"Categories"},
     *      summary="List all catergories",
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
    public function listAll(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 20;

        $allowedSortBy = ['title'];
        $sortBy = in_array($request->sortBy, $allowedSortBy) ? $request->sortBy : null;
        $sortByDirection = $request->boolean('desc', false) ? 'desc' : 'asc';

        $categories = Category::when($request->title, fn ($sql, $value) => $sql->where('title', 'like', "%{$value}%"))
            ->when(!empty($sortBy), fn ($sql) => $sql->orderBy($sortBy, $sortByDirection))
            ->paginate($limit);

        return RespondWith::raw($categories);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/category/{uuid}",
     *      operationId="fetchOneCategory",
     *      tags={"Categories"},
     *      summary="Fetch a category",
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
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      ),
     *   )
     */
    public function findOne(string $uuid): JsonResponse
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();
        return RespondWith::success($category->toArray());
    }

    /**
     * @OA\Post(
     *      path="/api/v1/category/create",
     *      operationId="createCategory",
     *      tags={"Categories"},
     *      summary="Create a new category",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"title"},
     *                  properties={
     *                      @OA\Property(
     *                          property="title",
     *                          description="Category title",
     *                          type="string",
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
     *          response=422,
     *          description="Unprocessable Entity",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      ),
     *  )
     */
    public function create(CategoryRequest $request): JsonResponse
    {
        $slug = Str::slug($request->title);
        $category = Category::create(['title' => $request->title, 'slug' => $slug]);

        return RespondWith::success($category->toArray());
    }

    /**
     * @OA\Put(
     *      path="/api/v1/category/{uuid}",
     *      operationId="updateCategory",
     *      tags={"Categories"},
     *      summary="Update an existing category",
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
     *                  required={"title"},
     *                  properties={
     *                      @OA\Property(
     *                          property="title",
     *                          description="Category title",
     *                          type="string",
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
    public function update(CategoryRequest $request, string $uuid): JsonResponse
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();

        $slug = Str::slug($request->title);
        $category->update(['title' => $request->title, 'slug' => $slug]);

        return RespondWith::success($category->toArray());
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/category/{uuid}",
     *      operationId="deleteCategory",
     *      tags={"Categories"},
     *      summary="Delete an existing category",
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
     *          response=422,
     *          description="Unprocessable Entity",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      ),
     *  )
     */
    public function delete(string $uuid): JsonResponse
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();
        $category->delete();

        return RespondWith::success();
    }
}
