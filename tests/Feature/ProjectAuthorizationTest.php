<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_authorized_to_manage_projects(): void
    {
        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $project = new Project();

        $this->assertTrue(
            Gate::forUser($admin)->allows('update', $project)
        );
    }

    public function test_author_is_not_authorized_to_manage_projects(): void
    {
        $authorRole = Role::create([
            'name' => 'Author',
            'slug' => 'author',
        ]);

        $author = User::factory()->create([
            'role_id' => $authorRole->id,
        ]);

        $project = new Project();

        $this->assertFalse(
            Gate::forUser($author)->allows('update', $project)
        );
    }

    public function test_guest_cannot_access_projects(): void
    {
        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(401);
    }

    public function test_author_cannot_access_projects(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_projects(): void
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_view_a_single_project(): void
    {
        $project = $this->createProject();

        $response = $this->getJson(
            "/api/v1/projects/{$project->id}"
        );

        $response->assertStatus(401);
    }

    public function test_author_cannot_view_a_single_project(): void
    {
        $author = $this->createAuthor();
        $project = $this->createProject();

        Sanctum::actingAs($author);

        $response = $this->getJson(
            "/api/v1/projects/{$project->id}"
        );

        $response->assertStatus(403);
    }

    public function test_admin_can_view_a_single_project(): void
    {
        $admin = $this->createAdmin();
        $project = $this->createProject();

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            "/api/v1/projects/{$project->id}"
        );

        $response->assertStatus(200);
    }

    public function test_guest_cannot_create_a_project(): void
    {
        $response = $this->postJson('/api/v1/projects', [
            'title' => 'Corporate Website',
            'slug' => 'corporate-website',
            'description' => 'A corporate website project.',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response->assertStatus(401);
    }

    public function test_author_cannot_create_a_project(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/projects', [
            'title' => 'Corporate Website',
            'slug' => 'corporate-website',
            'description' => 'A corporate website project.',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_create_a_project(): void
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/projects', [
            'title' => 'Corporate Website',
            'slug' => 'corporate-website',
            'description' => 'A corporate website project.',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response->assertStatus(201);
    }

    public function test_guest_cannot_update_a_project(): void
    {
        $project = $this->createProject();

        $response = $this->putJson(
            "/api/v1/projects/{$project->id}",
            [
                'title' => 'Updated Corporate Website',
                'slug' => 'updated-corporate-website',
                'status' => 'published',
                'sort_order' => 0,
            ]
        );

        $response->assertStatus(401);
    }

    public function test_author_cannot_update_a_project(): void
    {
        $author = $this->createAuthor();
        $project = $this->createProject();

        Sanctum::actingAs($author);

        $response = $this->putJson(
            "/api/v1/projects/{$project->id}",
            [
                'title' => 'Updated Corporate Website',
                'slug' => 'updated-corporate-website',
                'status' => 'published',
                'sort_order' => 0,
            ]
        );

        $response->assertStatus(403);
    }

    public function test_admin_can_update_a_project(): void
    {
        $admin = $this->createAdmin();
        $project = $this->createProject();

        Sanctum::actingAs($admin);

        $response = $this->putJson(
            "/api/v1/projects/{$project->id}",
            [
                'title' => 'Updated Corporate Website',
                'slug' => 'updated-corporate-website',
                'status' => 'published',
                'sort_order' => 0,
            ]
        );

        $response->assertStatus(200);
    }

    public function test_guest_cannot_delete_a_project(): void
    {
        $project = $this->createProject();

        $response = $this->deleteJson(
            "/api/v1/projects/{$project->id}"
        );

        $response->assertStatus(401);
    }

    public function test_author_cannot_delete_a_project(): void
    {
        $author = $this->createAuthor();
        $project = $this->createProject();

        Sanctum::actingAs($author);

        $response = $this->deleteJson(
            "/api/v1/projects/{$project->id}"
        );

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_a_project(): void
    {
        $admin = $this->createAdmin();
        $project = $this->createProject();

        Sanctum::actingAs($admin);

        $response = $this->deleteJson(
            "/api/v1/projects/{$project->id}"
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

    private function createProject(): Project
    {
        return Project::create([
            'title' => 'Corporate Website',
            'slug' => 'corporate-website',
            'short_description' => 'Professional corporate website.',
            'description' => 'A complete corporate website project.',
            'status' => 'draft',
            'sort_order' => 0,
            'published_at' => null,
        ]);
    }
}