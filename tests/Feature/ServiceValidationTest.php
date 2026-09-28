<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A service requires the required fields.
     */
    public function test_service_creation_requires_required_fields(): void
    {
        $response = $this->postJson('/api/v1/services', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'service_category_id',
            'title',
            'slug',
            'description',
            'status',
            'sort_order',
        ]);
    }

    /**
     * A service category must exist.
     */
    public function test_service_category_must_exist(): void
    {
        $response = $this->postJson('/api/v1/services', [
            'service_category_id' => 999999,
            'title' => 'Web Design',
            'slug' => 'web-design',
            'description' => 'Professional web design service.',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'service_category_id',
        ]);
    }

    /**
     * A service can be created with valid data.
     */
    public function test_service_can_be_created_with_valid_data(): void
    {
        $category = $this->createCategory();

        $response = $this->postJson('/api/v1/services', [
            'service_category_id' => $category->id,
            'title' => 'Corporate Website',
            'slug' => 'corporate-website',
            'short_description' => 'Professional corporate website.',
            'description' => 'A complete corporate website service.',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response->assertStatus(201);

        $response->assertJsonPath(
            'data.title',
            'Corporate Website'
        );

        $this->assertDatabaseHas('services', [
            'slug' => 'corporate-website',
            'title' => 'Corporate Website',
        ]);
    }

    /**
     * A service can be updated.
     */
    public function test_service_can_be_updated(): void
    {
        $category = $this->createCategory();

        $service = $this->createService($category);

        $response = $this->putJson(
            "/api/v1/services/{$service->id}",
            [
                'title' => 'Updated Corporate Website',
                'slug' => 'updated-corporate-website',
                'status' => 'published',
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

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'title' => 'Updated Corporate Website',
            'slug' => 'updated-corporate-website',
            'status' => 'published',
        ]);
    }

    /**
     * A service can be deleted.
     */
    public function test_service_can_be_deleted(): void
    {
        $category = $this->createCategory();

        $service = $this->createService($category);

        $response = $this->deleteJson(
            "/api/v1/services/{$service->id}"
        );

        $response->assertStatus(204);

        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);
    }

    /**
     * Updating a non-existent service returns 404.
     */
    public function test_updating_non_existent_service_returns_not_found(): void
    {
        $response = $this->putJson(
            '/api/v1/services/999999',
            [
                'title' => 'Updated Service',
            ]
        );

        $response->assertStatus(404);
    }

    /**
     * Deleting a non-existent service returns 404.
     */
    public function test_deleting_non_existent_service_returns_not_found(): void
    {
        $response = $this->deleteJson(
            '/api/v1/services/999999'
        );

        $response->assertStatus(404);
    }

    /**
     * Services can be listed.
     */
    public function test_services_can_be_listed(): void
    {
        $category = $this->createCategory();

        $this->createService($category);

        $response = $this->getJson('/api/v1/services');

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
     * An empty service list returns an empty data array.
     */
    public function test_empty_service_list_returns_empty_data(): void
    {
        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(200);

        $response->assertJsonCount(0, 'data');

        $response->assertJson([
            'data' => [],
        ]);
    }

    /**
     * A service can be retrieved by ID.
     */
    public function test_service_can_be_retrieved(): void
    {
        $category = $this->createCategory();

        $service = $this->createService($category);

        $response = $this->getJson(
            "/api/v1/services/{$service->id}"
        );

        $response->assertStatus(200);

        $response->assertJsonPath(
            'data.id',
            $service->id
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
     * Retrieving a non-existent service returns 404.
     */
    public function test_retrieving_non_existent_service_returns_not_found(): void
    {
        $response = $this->getJson(
            '/api/v1/services/999999'
        );

        $response->assertStatus(404);
    }


    /**
     * Services are paginated correctly.
     */
    public function test_services_are_paginated_correctly(): void
    {
        $category = $this->createCategory();

        for ($i = 1; $i <= 15; $i++) {
            Service::create([
                'service_category_id' => $category->id,
                'title' => "Service {$i}",
                'slug' => "service-{$i}",
                'short_description' => "Short description {$i}.",
                'description' => "Description {$i}.",
                'status' => 'draft',
                'sort_order' => $i,
            ]);
        }

        $pageOne = $this->getJson('/api/v1/services?page=1');

        $pageOne->assertStatus(200);
        $pageOne->assertJsonCount(10, 'data');
        $pageOne->assertJsonPath('meta.current_page', 1);
        $pageOne->assertJsonPath('meta.last_page', 2);
        $pageOne->assertJsonPath('meta.total', 15);

        $pageTwo = $this->getJson('/api/v1/services?page=2');

        $pageTwo->assertStatus(200);
        $pageTwo->assertJsonCount(5, 'data');
        $pageTwo->assertJsonPath('meta.current_page', 2);
        $pageTwo->assertJsonPath('meta.last_page', 2);
        $pageTwo->assertJsonPath('meta.total', 15);
    }

    /**
     * Create a service category for tests.
     */
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

    /**
     * Create a service for tests.
     */
    private function createService(ServiceCategory $category): Service
    {
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
