<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        Sanctum::actingAs($admin);
    }

    /**
     * A project requires the required fields.
     */
    public function test_project_creation_requires_required_fields(): void
    {
        $response = $this->postJson('/api/v1/projects', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'title',
            'slug',
            'status',
            'sort_order',
        ]);
    }

    /**
     * A project can be created with valid data.
     */
    public function test_project_can_be_created_with_valid_data(): void
    {
        $response = $this->postJson('/api/v1/projects', [
            'title' => 'Corporate Website',
            'slug' => 'corporate-website',
            'short_description' => 'Professional corporate website.',
            'description' => 'A complete corporate website project.',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response->assertStatus(201);

        $response->assertJsonPath(
            'data.title',
            'Corporate Website'
        );

        $this->assertDatabaseHas('projects', [
            'slug' => 'corporate-website',
            'title' => 'Corporate Website',
        ]);
    }

    /**
     * A project slug must be unique.
     */
    public function test_project_slug_must_be_unique(): void
    {
        $this->createProject();

        $response = $this->postJson('/api/v1/projects', [
            'title' => 'Another Project',
            'slug' => 'corporate-website',
            'description' => 'Another project.',
            'status' => 'draft',
            'sort_order' => 1,
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'slug',
        ]);
    }

    /**
     * Project status must be valid.
     */
    public function test_project_status_must_be_valid(): void
    {
        $response = $this->postJson('/api/v1/projects', [
            'title' => 'Corporate Website',
            'slug' => 'corporate-website',
            'description' => 'A corporate website project.',
            'status' => 'invalid',
            'sort_order' => 0,
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'status',
        ]);
    }

    /**
     * Project sort order must be zero or greater.
     */
    public function test_project_sort_order_cannot_be_negative(): void
    {
        $response = $this->postJson('/api/v1/projects', [
            'title' => 'Corporate Website',
            'slug' => 'corporate-website',
            'description' => 'A corporate website project.',
            'status' => 'draft',
            'sort_order' => -1,
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'sort_order',
        ]);
    }

    /**
     * A project can be updated.
     */
    public function test_project_can_be_updated(): void
    {
        $project = $this->createProject();

        $response = $this->putJson(
            "/api/v1/projects/{$project->id}",
            [
                'title' => 'Updated Corporate Website',
                'slug' => 'updated-corporate-website',
                'status' => 'published',
                'sort_order' => 1,
            ]
        );

        $response->assertStatus(200);

        $response->assertJsonPath(
            'data.title',
            'Updated Corporate Website'
        );

        $response->assertJsonPath(
            'data.status',
            'published'
        );

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Corporate Website',
            'slug' => 'updated-corporate-website',
            'status' => 'published',
        ]);
    }

    /**
     * A project can keep its own slug when updated.
     */
    public function test_project_can_be_updated_without_changing_its_slug(): void
    {
        $project = $this->createProject();

        $response = $this->putJson(
            "/api/v1/projects/{$project->id}",
            [
                'title' => 'Updated Corporate Website',
                'slug' => 'corporate-website',
                'status' => 'published',
                'sort_order' => 1,
            ]
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'slug' => 'corporate-website',
        ]);
    }

    /**
     * A project can be deleted.
     */
    public function test_project_can_be_deleted(): void
    {
        $project = $this->createProject();

        $response = $this->deleteJson(
            "/api/v1/projects/{$project->id}"
        );

        $response->assertStatus(204);

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    /**
     * Updating a non-existent project returns 404.
     */
    public function test_updating_non_existent_project_returns_not_found(): void
    {
        $response = $this->putJson(
            '/api/v1/projects/999999',
            [
                'title' => 'Updated Project',
                'slug' => 'updated-project',
                'status' => 'published',
                'sort_order' => 0,
            ]
        );

        $response->assertStatus(404);
    }

    /**
     * Deleting a non-existent project returns 404.
     */
    public function test_deleting_non_existent_project_returns_not_found(): void
    {
        $response = $this->deleteJson(
            '/api/v1/projects/999999'
        );

        $response->assertStatus(404);
    }

    /**
     * Projects can be listed.
     */
    public function test_projects_can_be_listed(): void
    {
        $this->createProject();

        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');

        $response->assertJsonPath(
            'data.0.title',
            'Corporate Website'
        );

        $response->assertJsonPath(
            'data.0.slug',
            'corporate-website'
        );
    }

    /**
     * An empty project list returns an empty data array.
     */
    public function test_empty_project_list_returns_empty_data(): void
    {
        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(200);

        $response->assertJsonCount(0, 'data');

        $response->assertJson([
            'data' => [],
        ]);
    }

    /**
     * A project can be retrieved by ID.
     */
    public function test_project_can_be_retrieved(): void
    {
        $project = $this->createProject();

        $response = $this->getJson(
            "/api/v1/projects/{$project->id}"
        );

        $response->assertStatus(200);

        $response->assertJsonPath(
            'data.id',
            $project->id
        );

        $response->assertJsonPath(
            'data.title',
            'Corporate Website'
        );

        $response->assertJsonPath(
            'data.slug',
            'corporate-website'
        );
    }

    /**
     * A single project response follows the standard resource structure.
     */
    public function test_single_project_response_follows_standard_structure(): void
    {
        $project = $this->createProject();

        $response = $this->getJson(
            "/api/v1/projects/{$project->id}"
        );

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'slug',
                'short_description',
                'description',
                'status',
                'sort_order',
                'published_at',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    /**
     * A project collection response follows the standard resource structure.
     */
    public function test_project_collection_response_follows_standard_structure(): void
    {
        $this->createProject();

        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'short_description',
                    'description',
                    'status',
                    'sort_order',
                    'published_at',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ]);
    }

    /**
     * Retrieving a non-existent project returns 404.
     */
    public function test_retrieving_non_existent_project_returns_not_found(): void
    {
        $response = $this->getJson(
            '/api/v1/projects/999999'
        );

        $response->assertStatus(404);
    }

    /**
     * Projects are paginated correctly.
     */
    public function test_projects_are_paginated_correctly(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            Project::create([
                'title' => "Project {$i}",
                'slug' => "project-{$i}",
                'short_description' => "Short description {$i}.",
                'description' => "Description {$i}.",
                'status' => 'draft',
                'sort_order' => $i,
            ]);
        }

        $pageOne = $this->getJson('/api/v1/projects?page=1');

        $pageOne->assertStatus(200);
        $pageOne->assertJsonCount(10, 'data');
        $pageOne->assertJsonPath('meta.current_page', 1);
        $pageOne->assertJsonPath('meta.last_page', 2);
        $pageOne->assertJsonPath('meta.total', 15);

        $pageTwo = $this->getJson('/api/v1/projects?page=2');

        $pageTwo->assertStatus(200);
        $pageTwo->assertJsonCount(5, 'data');
        $pageTwo->assertJsonPath('meta.current_page', 2);
        $pageTwo->assertJsonPath('meta.last_page', 2);
        $pageTwo->assertJsonPath('meta.total', 15);
    }

    /**
     * Create a project for tests.
     */
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