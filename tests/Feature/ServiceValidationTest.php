<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ServiceValidationTest extends TestCase
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
     * A single service response follows the standard resource structure.
     */
    public function test_single_service_response_follows_standard_structure(): void
    {
        $category = $this->createCategory();

        $service = $this->createService($category);

        $response = $this->getJson(
            "/api/v1/services/{$service->id}"
        );

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'service_category_id',
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
     * A service collection response follows the standard resource structure.
     */
    public function test_service_collection_response_follows_standard_structure(): void
    {
        $category = $this->createCategory();

        $this->createService($category);

        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'service_category_id',
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
     * Services can be filtered by status.
     */
    public function test_services_can_be_filtered_by_status(): void
    {
        $category = $this->createCategory();

        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Published Service',
            'slug' => 'published-service',
            'short_description' => 'Published service.',
            'description' => 'Published service description.',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Draft Service',
            'slug' => 'draft-service',
            'short_description' => 'Draft service.',
            'description' => 'Draft service description.',
            'status' => 'draft',
            'sort_order' => 2,
        ]);

        $response = $this->getJson(
            '/api/v1/services?status=published'
        );

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $response->assertJsonPath(
            'data.0.title',
            'Published Service'
        );

        $response->assertJsonPath(
            'data.0.status',
            'published'
        );
    }

    /**
     * Services can be filtered by service category.
     */
    public function test_services_can_be_filtered_by_service_category(): void
    {
        $categoryOne = $this->createCategory();

        $categoryTwo = ServiceCategory::create([
            'name' => 'SEO',
            'slug' => 'seo',
            'description' => 'SEO services.',
            'status' => 'active',
            'sort_order' => 1,
        ]);

        Service::create([
            'service_category_id' => $categoryOne->id,
            'title' => 'Web Design Service',
            'slug' => 'web-design-service',
            'short_description' => 'Web design service.',
            'description' => 'Web design service description.',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        Service::create([
            'service_category_id' => $categoryTwo->id,
            'title' => 'SEO Service',
            'slug' => 'seo-service',
            'short_description' => 'SEO service.',
            'description' => 'SEO service description.',
            'status' => 'published',
            'sort_order' => 2,
        ]);

        $response = $this->getJson(
            "/api/v1/services?service_category_id={$categoryTwo->id}"
        );

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $response->assertJsonPath(
            'data.0.title',
            'SEO Service'
        );

        $response->assertJsonPath(
            'data.0.service_category_id',
            $categoryTwo->id
        );
    }

    /**
     * Services can be sorted by sort order ascending.
     */
    public function test_services_can_be_sorted_by_sort_order_ascending(): void
    {
        $category = $this->createCategory();

        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Service C',
            'slug' => 'service-c',
            'short_description' => 'Service C.',
            'description' => 'Service C description.',
            'status' => 'published',
            'sort_order' => 3,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Service A',
            'slug' => 'service-a',
            'short_description' => 'Service A.',
            'description' => 'Service A description.',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Service B',
            'slug' => 'service-b',
            'short_description' => 'Service B.',
            'description' => 'Service B description.',
            'status' => 'published',
            'sort_order' => 2,
        ]);

        $response = $this->getJson(
            '/api/v1/services?sort=sort_order'
        );

        $response->assertStatus(200);

        $response->assertJsonPath('data.0.title', 'Service A');
        $response->assertJsonPath('data.1.title', 'Service B');
        $response->assertJsonPath('data.2.title', 'Service C');
    }

    /**
     * Services can be sorted by sort order descending.
     */
    public function test_services_can_be_sorted_by_sort_order_descending(): void
    {
        $category = $this->createCategory();

        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Service A',
            'slug' => 'service-a-desc',
            'short_description' => 'Service A.',
            'description' => 'Service A description.',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Service B',
            'slug' => 'service-b-desc',
            'short_description' => 'Service B.',
            'description' => 'Service B description.',
            'status' => 'published',
            'sort_order' => 2,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Service C',
            'slug' => 'service-c-desc',
            'short_description' => 'Service C.',
            'description' => 'Service C description.',
            'status' => 'published',
            'sort_order' => 3,
        ]);

        $response = $this->getJson(
            '/api/v1/services?sort=-sort_order'
        );

        $response->assertStatus(200);

        $response->assertJsonPath('data.0.title', 'Service C');
        $response->assertJsonPath('data.1.title', 'Service B');
        $response->assertJsonPath('data.2.title', 'Service A');
    }

    /**
     * Services can be searched by title.
     */
    public function test_services_can_be_searched_by_title(): void
    {
        $category = $this->createCategory();

        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Web Design Service',
            'slug' => 'web-design-service',
            'short_description' => 'Professional web design.',
            'description' => 'Complete web design solution.',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'title' => 'SEO Service',
            'slug' => 'seo-service-search-title',
            'short_description' => 'Professional SEO service.',
            'description' => 'Complete SEO solution.',
            'status' => 'published',
            'sort_order' => 2,
        ]);

        $response = $this->getJson(
            '/api/v1/services?search=Design'
        );

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $response->assertJsonPath(
            'data.0.title',
            'Web Design Service'
        );
    }

    /**
     * Services can be searched by description.
     */
    public function test_services_can_be_searched_by_description(): void
    {
        $category = $this->createCategory();

        Service::create([
            'service_category_id' => $category->id,
            'title' => 'Corporate Website',
            'slug' => 'corporate-website-search-description',
            'short_description' => 'Professional website service.',
            'description' => 'Advanced e-commerce development solution.',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        Service::create([
            'service_category_id' => $category->id,
            'title' => 'SEO Service',
            'slug' => 'seo-service-search-description',
            'short_description' => 'Professional SEO service.',
            'description' => 'Complete search engine optimization solution.',
            'status' => 'published',
            'sort_order' => 2,
        ]);

        $response = $this->getJson(
            '/api/v1/services?search=e-commerce'
        );

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');

        $response->assertJsonPath(
            'data.0.title',
            'Corporate Website'
        );
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

