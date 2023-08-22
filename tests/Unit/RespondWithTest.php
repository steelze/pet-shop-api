<?php

namespace Tests\Unit;

use App\Helpers\RespondWith;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RespondWithTest extends TestCase
{
    public function test_success_response(): void
    {
        $data = ['message' => 'Data retrieved successfully'];

        $response = RespondWith::success($data, Response::HTTP_CREATED);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $responseData = $response->getData(true);

        $this->assertEquals(1, $responseData['success']);
        $this->assertEquals($data, $responseData['data']);
    }

    public function test_error_response(): void
    {
        $errors = [
            'name' => [
                'Name is required',
                'Name must be a string',
            ]
        ];

        $response = RespondWith::error('Validation error', $errors, Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $responseData = $response->getData(true);

        $this->assertEquals(0, $responseData['success']);
        $this->assertEquals($errors, $responseData['errors']);
    }
}
