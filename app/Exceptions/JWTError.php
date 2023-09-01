<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class JWTError extends Exception
{
    public function getStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }
}
