<?php

namespace Tests\Feature\Authentication\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_request_token_with_empty_credentials(): void
    {
        $response = $this->postJson('/api/v1/user/forgot-password', ['email' => '']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'errors.email'])->etc());
    }

    public function test_user_cannot_request_token_with_wrong_email(): void
    {
        $response = $this->postJson('/api/v1/user/forgot-password', ['success', 'email' => fake()->email()]);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_admin_cannot_request_token(): void
    {
        $user = User::factory()->admin()->create();
        $response = $this->postJson('/api/v1/user/forgot-password', ['email' => $user->email]);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_user_can_request_token_successfully(): void
    {
        $user = User::factory()->user()->create();
        $response = $this->postJson('/api/v1/user/forgot-password', ['email' => $user->email]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'data.reset_token'])->etc());
    }
}
