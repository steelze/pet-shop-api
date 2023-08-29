<?php

namespace Tests\Feature\Auth\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_login_with_empty_credentials(): void
    {
        $response = $this->postJson('/api/v1/admin/login', ['email' => '', 'password' => '']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'errors.email', 'errors.password'])->etc());
    }

    public function test_admin_cannot_login_with_wrong_credentials(): void
    {
        $admin = User::factory()->create();
        $response = $this->postJson('/api/v1/admin/login', ['success', 'email' => $admin->email, 'password' => '!@#$%*&^']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_user_cannot_login_successful(): void
    {
        $admin = User::factory()->user()->state(['password' => 'password'])->create();
        $response = $this->postJson('/api/v1/admin/login', ['email' => $admin->email, 'password' => 'password']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_admin_can_login_successful(): void
    {
        $admin = User::factory()->admin()->state(['password' => 'password'])->create();
        $response = $this->postJson('/api/v1/admin/login', ['email' => $admin->email, 'password' => 'password']);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'data.token'])->etc());
    }
}
