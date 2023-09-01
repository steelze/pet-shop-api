<?php

namespace App\Http\Controllers;

use App\Helpers\RespondWith;
use App\Http\Requests\OrderStatusRequest;
use App\Models\OrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/order-statuses",
     *      operationId="getAllOrder Statuses",
     *      tags={"Order Statuses"},
     *      summary="List all order status",
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

        $categories = OrderStatus::when(
                $request->title,
                fn ($sql, $value) => $sql->where('title', 'like', "%{$value}%")
            )
            ->when(!empty($sortBy), fn ($sql) => $sql->orderBy($sortBy, $sortByDirection))
            ->paginate($limit);

        return RespondWith::raw($categories);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/order-statuse/{uuid}",
     *      operationId="fetchOneOrderStatus",
     *      tags={"Order Statuses"},
     *      summary="Fetch an order status",
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
        $status = OrderStatus::where('uuid', $uuid)->firstOrFail();
        return RespondWith::success($status);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/order-status/create",
     *      operationId="createOrderStatus",
     *      tags={"Order Statuses"},
     *      summary="Create a new order status",
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
     *                          description="OrderStatus title",
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
    public function create(OrderStatusRequest $request): JsonResponse
    {
        $status = OrderStatus::create(['title' => $request->title]);

        return RespondWith::success($status);
    }

    /**
     * @OA\Put(
     *      path="/api/v1/order-status/{uuid}",
     *      operationId="updateOrderStatus",
     *      tags={"Order Statuses"},
     *      summary="Update an existing order status",
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
     *                          description="OrderStatus title",
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
    public function update(OrderStatusRequest $request, string $uuid): JsonResponse
    {
        $status = OrderStatus::where('uuid', $uuid)->firstOrFail();

        $status->update(['title' => $request->title]);

        return RespondWith::success($status);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/order-status/{uuid}",
     *      operationId="deleteOrderStatus",
     *      tags={"Order Statuses"},
     *      summary="Delete an existing order status",
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
        $status = OrderStatus::where('uuid', $uuid)->firstOrFail();
        $status->delete();

        return RespondWith::success();
    }
}
