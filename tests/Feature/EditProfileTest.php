<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JWTService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EditProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_edit_profile(): void
    {
        $response = $this->putJson('/api/v1/user/edit', []);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_admin_cannot_edit_profile_with_user_endpoint(): void
    {
        $admin = User::factory()->admin()->create();
        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->putJson('/api/v1/user/edit', []);
        $response->assertStatus(Response::HTTP_FORBIDDEN)
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

    public function test_user_cannot_edit_profile_with_existing_email(): void
    {
        $email = 'invalid@email.com';

        User::factory()->user()->create(['email' => $email]);
        $user = User::factory()->user()->create();

        $token = (new JWTService())->fromUser($user)->toString();

        $payload = [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->firstName(),
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'avatar' => fake()->uuid(),
            'address' => fake()->address(),
            'phone_number' => '08162192832',
            'is_marketing' => fake()->boolean(),
        ];

        $response = $this->withToken($token)->putJson('/api/v1/user/edit', $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'errors.email'])->etc());
    }

    public function test_user_can_edit_profile(): void
    {
        $user = User::factory()->user()->create();
        $token = (new JWTService())->fromUser($user)->toString();

        $payload = [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->firstName(),
            'email' => $user->email,
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
    }

    public function test_unauthenticated_admin_cannot_edit_a_user_profile(): void
    {
        $user = User::factory()->user()->create();

        $response = $this->putJson("/api/v1/admin/user-edit/$user->uuid", []);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_user_cannot_edit_profile_with_admin_endpoint(): void
    {
        $user = User::factory()->user()->create();
        $token = (new JWTService())->fromUser($user)->toString();

        $response = $this->withToken($token)->putJson("/api/v1/admin/user-edit/$user->uuid", []);
        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_admin_cannot_edit_an_admin_profile(): void
    {
        $user = User::factory()->admin()->create();
        $admin = User::factory()->admin()->create();

        $token = (new JWTService())->fromUser($admin)->toString();

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

        $response = $this->withToken($token)->putJson("/api/v1/admin/user-edit/$user->uuid", $payload);
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_admin_cannot_edit_a_user_profile_with_empty_credentials(): void
    {
        $user = User::factory()->user()->create();
        $admin = User::factory()->admin()->create();

        $token = (new JWTService())->fromUser($admin)->toString();

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

        $response = $this->withToken($token)->putJson("/api/v1/admin/user-edit/$user->uuid", $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll([
                'success', 'errors.first_name', 'errors.last_name', 'errors.password',
                'errors.address', 'errors.phone_number',
            ])->etc());
    }

    public function test_admin_cannot_edit_a_user_profile_with_existing_email(): void
    {
        $email = 'invalid@email.com';

        User::factory()->user()->create(['email' => $email]);
        $user = User::factory()->user()->create();
        $admin = User::factory()->admin()->create();

        $token = (new JWTService())->fromUser($admin)->toString();

        $payload = [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->firstName(),
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'avatar' => fake()->uuid(),
            'address' => fake()->address(),
            'phone_number' => '08162192832',
            'is_marketing' => fake()->boolean(),
        ];

        $response = $this->withToken($token)->putJson("/api/v1/admin/user-edit/$user->uuid", $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'errors.email'])->etc());
    }

    public function test_admin_can_edit_a_user_profile(): void
    {
        $user = User::factory()->user()->create();
        $admin = User::factory()->admin()->create();

        $token = (new JWTService())->fromUser($admin)->toString();

        $payload = [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->firstName(),
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'avatar' => fake()->uuid(),
            'address' => fake()->address(),
            'phone_number' => '08162192832',
            'is_marketing' => fake()->boolean(),
        ];

        $response = $this->withToken($token)->putJson("/api/v1/admin/user-edit/$user->uuid", $payload);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'data', 'data.uuid'])->etc());
    }
}
