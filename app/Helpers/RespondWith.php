<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RespondWith
{
    /**
     * @param mixed $data
     */
    public static function raw(mixed $data, int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * @param array<mixed, mixed> $data
     * @param array<mixed, mixed> $extra
     */
    public static function success(array $data = [], int $code = Response::HTTP_OK, array $extra = []): JsonResponse
    {
        return self::respond(true, $code, $data, extra: $extra);
    }

    /**
     * @param array<mixed, mixed> $errors
     * @param array<mixed, mixed> $trace
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
     * @param array<mixed, mixed> $data
     * @param array<mixed, mixed> $errors
     * @param array<mixed, mixed> $extra
     * @param array<mixed, mixed> $trace
     */
    protected static function respond(
        bool $success,
        int $code,
        array $data = [],
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
