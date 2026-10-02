<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ServiceAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_authorized_to_manage_services(): void
    {
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $service = new Service();

        $this->assertTrue(
            Gate::forUser($admin)->allows('update', $service)
        );
    }

    public function test_author_is_not_authorized_to_manage_services(): void
    {
        $authorRole = Role::create([
            'name' => 'Author',
            'slug' => 'author',
        ]);

        $author = User::factory()->create([
            'role_id' => $authorRole->id,
        ]);

        $service = new Service();

        $this->assertFalse(
            Gate::forUser($author)->allows('update', $service)
        );
    }

    public function test_guest_cannot_access_services(): void
    {
        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(401);
    }

    public function test_author_cannot_access_services(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_services(): void
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_view_a_single_service(): void
    {
        $service = $this->createService();

        $response = $this->getJson(
            "/api/v1/services/{$service->id}"
        );

        $response->assertStatus(401);
    }

    public function test_author_cannot_view_a_single_service(): void
    {
        $author = $this->createAuthor();
        $service = $this->createService();

        Sanctum::actingAs($author);

        $response = $this->getJson(
            "/api/v1/services/{$service->id}"
        );

        $response->assertStatus(403);
    }

    public function test_admin_can_view_a_single_service(): void
    {
        $admin = $this->createAdmin();
        $service = $this->createService();

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            "/api/v1/services/{$service->id}"
        );

        $response->assertStatus(200);
    }

    public function test_guest_cannot_create_a_service(): void
    {
        $category = $this->createCategory();

        $response = $this->postJson('/api/v1/services', [
            'service_category_id' => $category->id,
            'title' => 'Corporate Website',
            'slug' => 'corporate-website',
            'description' => 'A complete corporate website service.',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response->assertStatus(401);
    }

    public function test_author_cannot_create_a_service(): void
    {
        $author = $this->createAuthor();
        $category = $this->createCategory();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/services', [
            'service_category_id' => $category->id,
            'title' => 'Corporate Website',
            'slug' => 'corporate-website',
            'description' => 'A complete corporate website service.',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_create_a_service(): void
    {
        $admin = $this->createAdmin();
        $category = $this->createCategory();

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/services', [
            'service_category_id' => $category->id,
            'title' => 'Corporate Website',
            'slug' => 'corporate-website',
            'description' => 'A complete corporate website service.',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response->assertStatus(201);
    }

    public function test_guest_cannot_update_a_service(): void
    {
        $service = $this->createService();

        $response = $this->putJson(
            "/api/v1/services/{$service->id}",
            [
                'title' => 'Updated Corporate Website',
                'slug' => 'updated-corporate-website',
                'status' => 'published',
            ]
        );

        $response->assertStatus(401);
    }

    public function test_author_cannot_update_a_service(): void
    {
        $author = $this->createAuthor();
        $service = $this->createService();

        Sanctum::actingAs($author);

        $response = $this->putJson(
            "/api/v1/services/{$service->id}",
            [
                'title' => 'Updated Corporate Website',
                'slug' => 'updated-corporate-website',
                'status' => 'published',
            ]
        );

        $response->assertStatus(403);
    }

    public function test_admin_can_update_a_service(): void
    {
        $admin = $this->createAdmin();
        $service = $this->createService();

        Sanctum::actingAs($admin);

        $response = $this->putJson(
            "/api/v1/services/{$service->id}",
            [
                'title' => 'Updated Corporate Website',
                'slug' => 'updated-corporate-website',
                'status' => 'published',
            ]
        );

        $response->assertStatus(200);
    }

    public function test_guest_cannot_delete_a_service(): void
    {
        $service = $this->createService();

        $response = $this->deleteJson(
            "/api/v1/services/{$service->id}"
        );

        $response->assertStatus(401);
    }

    public function test_author_cannot_delete_a_service(): void
    {
        $author = $this->createAuthor();
        $service = $this->createService();

        Sanctum::actingAs($author);

        $response = $this->deleteJson(
            "/api/v1/services/{$service->id}"
        );

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_a_service(): void
    {
        $admin = $this->createAdmin();
        $service = $this->createService();

        Sanctum::actingAs($admin);

        $response = $this->deleteJson(
            "/api/v1/services/{$service->id}"
        );

        $response->assertStatus(204);
    }

    private function createAdmin(): User
    {
        $role = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

    private function createAuthor(): User
    {
        $role = Role::create([
            'name' => 'Author',
            'slug' => 'author',
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

    private function createCategory(): ServiceCategory
    {
        return ServiceCategory::create([
            'name' => 'Web Design',
            'slug' => 'web-design',
            'description' => 'Web design services.',
            'status' => 'active',
            'sort_order' => 0,
        ]);
    }

    private function createService(): Service
    {
        $category = $this->createCategory();

        return Service::create([
            'service_category_id' => $category->id,
            'title' => 'Corporate Website',
            'slug' => 'corporate-website',
            'short_description' => 'Professional corporate website.',
            'description' => 'A complete corporate website service.',
            'status' => 'draft',
            'sort_order' => 0,
        ]);
    }
}