<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JWTService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_edit_profile(): void
    {
        $payload = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
            'avatar' => '',
            'address' => '',
            'phone_number' => '',
            'is_marketing' => '',
        ];

        $response = $this->putJson('/api/v1/user/edit', $payload);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_user_cannot_edit_profile_with_empty_credentials(): void
    {
        $user = User::factory()->user()->create();
        $token = (new JWTService())->fromUser($user)->toString();

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

        $response = $this->withToken($token)->putJson('/api/v1/user/edit', $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll([
                'success', 'errors.first_name', 'errors.last_name', 'errors.password',
                'errors.address', 'errors.phone_number',
            ])->etc());
    }

    public function test_user_can_edit_profile(): void
    {
        $user = User::factory()->user()->create();
        $token = (new JWTService())->fromUser($user)->toString();

        $payload = [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->firstName(),
            'email' => fake()->email(),
            'password' => 'password',
            'password_confirmation' => 'password',
            'avatar' => fake()->uuid(),
            'address' => fake()->address(),
            'phone_number' => '08162192832',
            'is_marketing' => fake()->boolean(),
        ];

        $response = $this->withToken($token)->putJson('/api/v1/user/edit', $payload);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'data', 'data.uuid'])->etc());

        $this->assertDatabaseHas(User::class, [
            'id' => $user->id,
            'uuid' => $user->uuid,
            'first_name' => $payload['first_name'],
            'last_name' => $payload['last_name'],
            'email' => $payload['email'],
            'avatar' => $payload['avatar'],
            'address' => $payload['address'],
            'phone_number' => $payload['phone_number'],
            'is_marketing' => $payload['is_marketing'],
        ]);
    }
}
