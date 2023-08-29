<?php

namespace Tests\Feature\Auth\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_create_account_with_empty_credentials(): void
    {
        $payload = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
            'avatar' => '',
            'address' => '',
            'phone_number' => '',
            'is_marketing' => '',
        ];

        $response = $this->postJson('/api/v1/admin/create', $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll([
                'success', 'errors.first_name', 'errors.last_name', 'errors.password',
                'errors.address', 'errors.phone_number',
            ])->etc());
    }

    public function test_admin_cannot_create_account_with_existing_email(): void
    {
        $user = User::factory()->create();
        $payload = [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->firstName(),
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'avatar' => fake()->uuid(),
            'address' => fake()->address(),
            'phone_number' => '08123456789',
            'is_marketing' => fake()->boolean(),
        ];

        $response = $this->postJson('/api/v1/admin/create', $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'errors.email'])->etc());
    }

    public function test_admin_can_create_account_successful(): void
    {
        $payload = [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->firstName(),
            'email' => fake()->email(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'avatar' => fake()->uuid(),
            'address' => fake()->address(),
            'phone_number' => '08123456789',
            'is_marketing' => fake()->boolean(),
        ];

        $response = $this->postJson('/api/v1/admin/create', $payload);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'data.uuid', 'data.token'])->etc());
    }
}
