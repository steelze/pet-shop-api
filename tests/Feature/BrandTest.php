<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use App\Services\JWTService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_create_brand(): void
    {
        $response = $this->postJson('/api/v1/brand/create');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_authenticated_user_cannot_create_brand_with_empty_payload(): void
    {
        $payload = ['title' => ''];
        $admin = User::factory()->admin()->create();

        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->postJson('/api/v1/brand/create', $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'errors.title'])->etc());
    }

    public function test_authenticated_user_can_create_brand(): void
    {
        $payload = ['title' => fake()->city()];
        $admin = User::factory()->admin()->create();

        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->postJson('/api/v1/brand/create', $payload);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'data'])->etc());

        $this->assertDatabaseHas(Brand::class, ['title' => $payload['title']]);
    }

    public function test_unauthenticated_user_cannot_update_brand(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->putJson("/api/v1/brand/$brand->uuid");
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_authenticated_user_cannot_update_brand_with_empty_payload(): void
    {
        $payload = ['title' => ''];
        $brand = Brand::factory()->create();

        $admin = User::factory()->admin()->create();
        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->putJson("/api/v1/brand/$brand->uuid", $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'errors.title'])->etc());
    }

    public function test_authenticated_user_cannot_update_non_existent_brand(): void
    {
        $payload = ['title' => fake()->city()];
        $uuid = Str::uuid();

        $admin = User::factory()->admin()->create();
        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->putJson("/api/v1/brand/$uuid", $payload);
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_authenticated_user_can_update_brand(): void
    {
        $payload = ['title' => fake()->city()];
        $brand = Brand::factory()->create();

        $admin = User::factory()->admin()->create();
        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->putJson("/api/v1/brand/$brand->uuid", $payload);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'data'])->etc());

        $this->assertDatabaseHas(Brand::class, ['uuid' => $brand->uuid, 'title' => $payload['title']]);
    }

    public function test_unauthenticated_user_cannot_delete_brand(): void
    {
        $brand = Brand::factory()->create();

        $response = $this->deleteJson("/api/v1/brand/$brand->uuid");
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_authenticated_user_cannot_delete_non_existent_brand(): void
    {
        $uuid = Str::uuid();

        $admin = User::factory()->admin()->create();
        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->deleteJson("/api/v1/brand/$uuid");
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_authenticated_user_can_delete_brand(): void
    {
        $brand = Brand::factory()->create();

        $admin = User::factory()->admin()->create();
        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->deleteJson("/api/v1/brand/$brand->uuid");
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'data'])->etc());

        $this->assertDatabaseMissing(Brand::class, ['uuid' => $brand->uuid]);
    }
}
