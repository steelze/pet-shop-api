<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Services\JWTService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_create_category(): void
    {
        $response = $this->postJson('/api/v1/category/create');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_authenticated_user_cannot_create_category_with_empty_payload(): void
    {
        $payload = ['title' => ''];
        $admin = User::factory()->admin()->create();

        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->postJson('/api/v1/category/create', $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'errors.title'])->etc());
    }

    public function test_authenticated_user_can_create_category(): void
    {
        $payload = ['title' => fake()->city()];
        $admin = User::factory()->admin()->create();

        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->postJson('/api/v1/category/create', $payload);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'data'])->etc());

        $this->assertDatabaseHas(Category::class, ['title' => $payload['title']]);
    }

    public function test_unauthenticated_user_cannot_update_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->putJson("/api/v1/category/$category->uuid");
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_authenticated_user_cannot_update_category_with_empty_payload(): void
    {
        $payload = ['title' => ''];
        $category = Category::factory()->create();

        $admin = User::factory()->admin()->create();
        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->putJson("/api/v1/category/$category->uuid", $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'errors.title'])->etc());
    }

    public function test_authenticated_user_cannot_update_non_existent_category(): void
    {
        $payload = ['title' => fake()->city()];
        $uuid = Str::uuid();

        $admin = User::factory()->admin()->create();
        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->putJson("/api/v1/category/$uuid", $payload);
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_authenticated_user_can_update_category(): void
    {
        $payload = ['title' => fake()->city()];
        $category = Category::factory()->create();

        $admin = User::factory()->admin()->create();
        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->putJson("/api/v1/category/$category->uuid", $payload);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'data'])->etc());

        $this->assertDatabaseHas(Category::class, ['uuid' => $category->uuid, 'title' => $payload['title']]);
    }

    public function test_unauthenticated_user_cannot_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/v1/category/$category->uuid");
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_authenticated_user_cannot_delete_non_existent_category(): void
    {
        $uuid = Str::uuid();

        $admin = User::factory()->admin()->create();
        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->deleteJson("/api/v1/category/$uuid");
        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'error'])->etc());
    }

    public function test_authenticated_user_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $admin = User::factory()->admin()->create();
        $token = (new JWTService())->fromUser($admin)->toString();

        $response = $this->withToken($token)->deleteJson("/api/v1/category/$category->uuid");
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(fn (AssertableJson $json) => $json->hasAll(['success', 'data'])->etc());

        $this->assertDatabaseMissing(Category::class, ['uuid' => $category->uuid]);
    }
}
