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
    /**
     * @OA\Get(
     *      path="/api/v1/brands",
     *      operationId="getAllBrands",
     *      tags={"Brands"},
     *      summary="List all brands",
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

        $brands = Brand::when($request->title, fn ($sql, $value) => $sql->where('title', 'like', "%{$value}%"))
            ->when(!empty($sortBy), fn ($sql) => $sql->orderBy($sortBy, $sortByDirection))
            ->paginate($limit);

        return RespondWith::raw($brands);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/brand/{uuid}",
     *      operationId="fetchOneBrand",
     *      tags={"Brands"},
     *      summary="Fetch a brand",
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
        $brand = Brand::where('uuid', $uuid)->firstOrFail();
        return RespondWith::success($brand);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/brand/create",
     *      operationId="createBrand",
     *      tags={"Brands"},
     *      summary="Create a new brand",
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
     *                          description="Brand title",
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
    public function create(BrandRequest $request): JsonResponse
    {
        $slug = Str::slug($request->title);
        $brand = Brand::create(['title' => $request->title, 'slug' => $slug]);

        return RespondWith::success($brand);
    }

    /**
     * @OA\Put(
     *      path="/api/v1/brand/{uuid}",
     *      operationId="updateBrand",
     *      tags={"Brands"},
     *      summary="Update an existing brand",
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
     *                          description="Brand title",
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
    public function update(BrandRequest $request, string $uuid): JsonResponse
    {
        $brand = Brand::where('uuid', $uuid)->firstOrFail();

        $slug = Str::slug($request->title);
        $brand->update(['title' => $request->title, 'slug' => $slug]);

        return RespondWith::success($brand);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/brand/{uuid}",
     *      operationId="deleteBrand",
     *      tags={"Brands"},
     *      summary="Delete an existing brand",
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
        $brand = Brand::where('uuid', $uuid)->firstOrFail();
        $brand->delete();

        return RespondWith::success();
    }
}
