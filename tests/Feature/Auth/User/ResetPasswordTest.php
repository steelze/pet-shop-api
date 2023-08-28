<?php

namespace Tests\Feature\Auth\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_reset_password_with_empty_credentials(): void
    {
        $payload = ['email' => '', 'token' => '', 'password' => ''];
        $response = $this->postJson('/api/v1/user/reset-password-token', $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'errors.email', 'errors.token', 'errors.password'])->etc());
    }

    public function test_user_cannot_reset_password_with_wrong_email(): void
    {
        $user = User::factory()->user()->create();
        $token = Password::sendResetLink(['email' => $user->email], fn($record, $token) => $token);
        $payload = [
            'email' => fake()->email(),
            'token' => $token,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/v1/user/reset-password-token', $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_user_cannot_reset_password_with_invalid_token(): void
    {
        $user = User::factory()->user()->create();
        Password::sendResetLink(['email' => $user->email], fn($record, $token) => $token);
        $payload = [
            'email' => $user->email,
            'token' => Str::random(64),
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/v1/user/reset-password-token', $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_user_can_reset_password_successfully(): void
    {
        $user = User::factory()->user()->create();
        $token = Password::sendResetLink(['email' => $user->email], fn($record, $token) => $token);
        $password = Str::random(8);

        $payload = [
            'email' => $user->email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->postJson('/api/v1/user/reset-password-token', $payload);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'data.message'])->etc());

        $user->refresh();

        $this->assertTrue(Hash::check($password, $user->password), 'Password sent to endpoint and stored in database does not match');
    }
}
