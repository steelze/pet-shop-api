<?php

namespace App\Exceptions;

use App\Helpers\RespondWith;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
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
        $this->reportable(function (Throwable $e) {
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
        $message = $e->getMessage();
        $errors = $e instanceof ValidationException ? $e->errors() : [];
        $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        $trace = method_exists($e, 'getTrace') ? $e->getTrace() : [];

        return app()->isLocal()
            ? RespondWith::error($message, $errors, $code, trace: $trace)
            : RespondWith::error($message, $errors, $code);
    }
}
