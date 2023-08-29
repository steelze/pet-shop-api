<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\JWTService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DeleteProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_admin_cannot_delete_a_user_profile(): void
    {
        $user = User::factory()->user()->create();

        $response = $this->deleteJson("/api/v1/admin/user-delete/$user->uuid");
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_user_cannot_delete_profile_with_admin_endpoint(): void
    {
        $user = User::factory()->user()->create();
        $token = (new JWTService())->fromUser($user)->toString();

        $response = $this->withToken($token)->deleteJson("/api/v1/admin/user-delete/$user->uuid");
        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_admin_cannot_delete_an_admin_profile(): void
    {
        $user = User::factory()->admin()->create();
        $admin = User::factory()->admin()->create();

        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->deleteJson("/api/v1/admin/user-delete/$user->uuid");
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_admin_cannot_delete_a_non_existing_user_profile(): void
    {
        $uuid = Str::uuid();
        $admin = User::factory()->admin()->create();

        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->deleteJson("/api/v1/admin/user-delete/$uuid");
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_admin_can_delete_a_user_profile(): void
    {
        $user = User::factory()->user()->create();
        $admin = User::factory()->admin()->create();

        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->deleteJson("/api/v1/admin/user-delete/$user->uuid");
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success'])->etc());

        $this->assertDatabaseMissing(User::class, ['id' => $user->id]);
    }
}
