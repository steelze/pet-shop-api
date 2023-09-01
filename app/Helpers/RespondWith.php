<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RespondWith
{
    public static function raw(mixed $data, int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * @param array<int|string, int|string|bool|array|object> $extra
     */
    public static function success(mixed $data = [], int $code = Response::HTTP_OK, array $extra = []): JsonResponse
    {
        return self::respond(true, $code, $data, extra: $extra);
    }

    /**
     * @param array<string, array<string>> $errors
     * @param array<int, array<array|int|object|string>> $trace
     */
    public static function error(
        string $error,
        array $errors = [],
        int $code = Response::HTTP_INTERNAL_SERVER_ERROR,
        array $trace = []
    ): JsonResponse {
        return self::respond(false, $code, error: $error, errors: $errors, trace: $trace);
    }

    /**
     * @param array<string, array<string>> $errors
     * @param array<int|string, int|string|bool|array|object> $extra
     * @param array<int, array<array|int|object|string>> $trace
     */
    protected static function respond(
        bool $success,
        int $code,
        mixed $data = [],
        ?string $error = null,
        array $errors = [],
        array $extra = [],
        array $trace = []
    ): JsonResponse {
        return response()->json([
            'success' => $success ? 1 : 0,
            'data' => $data,
            'error' => $error,
            'errors' => $errors,
            'extra' => $extra,
            'trace' => $trace,
        ], $code);
    }
}
