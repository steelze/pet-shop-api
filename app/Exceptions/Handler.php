<?php

namespace App\Exceptions;

use App\Helpers\RespondWith;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e): void {
            //
        });

        $this->renderable(function (Throwable $e) {
            if (request()->expectsJson()) {
                return $this->renderJsonResponse($e);
            }
        });
    }

    protected function renderJsonResponse(Throwable $e): JsonResponse
    {
        $message = match (true) {
            $e instanceof ValidationException => 'Failed Validation',
            $e instanceof AccessDeniedHttpException => 'Unauthorized: Not enough privileges',
            $e->getPrevious() instanceof ModelNotFoundException => Str::of($e->getMessage())->afterLast('\\')->trim('].')->append(' not found'),
            default => $e->getMessage(),
        };

        $code = match (true) {
            $e instanceof ValidationException => Response::HTTP_UNPROCESSABLE_ENTITY,
            method_exists($e, 'getStatusCode') => $e->getStatusCode(),
            default => Response::HTTP_INTERNAL_SERVER_ERROR,
        };

        $errors = $e instanceof ValidationException ? $e->errors() : [];
        $trace = method_exists($e, 'getTrace') ? $e->getTrace() : [];

        return app()->isLocal()
            ? RespondWith::error($message, $errors, $code, trace: $trace)
            : RespondWith::error($message, $errors, $code);
    }
}
