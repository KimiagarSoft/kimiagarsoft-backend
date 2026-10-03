<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArticleFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_articles_can_be_filtered_by_published_status(): void
    {
        $admin = $this->createAdmin();

        Article::factory()->create([
            'user_id' => $admin->id,
            'status' => 'published',
        ]);

        Article::factory()->create([
            'user_id' => $admin->id,
            'status' => 'draft',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            '/api/v1/articles?status=published'
        );

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'published')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_articles_can_be_filtered_by_draft_status(): void
    {
        $admin = $this->createAdmin();

        Article::factory()->create([
            'user_id' => $admin->id,
            'status' => 'published',
        ]);

        Article::factory()->create([
            'user_id' => $admin->id,
            'status' => 'draft',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            '/api/v1/articles?status=draft'
        );

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'draft')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_author_status_filter_only_returns_own_articles(): void
    {
        $author = $this->createAuthor();
        $otherAuthor = $this->createAuthor();

        Article::factory()->create([
            'user_id' => $author->id,
            'status' => 'published',
        ]);

        Article::factory()->create([
            'user_id' => $author->id,
            'status' => 'draft',
        ]);

        Article::factory()->create([
            'user_id' => $otherAuthor->id,
            'status' => 'published',
        ]);

        Sanctum::actingAs($author);

        $response = $this->getJson(
            '/api/v1/articles?status=published'
        );

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.user_id', $author->id)
            ->assertJsonPath('data.0.status', 'published')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_invalid_status_filter_returns_empty_result(): void
    {
        $admin = $this->createAdmin();

        Article::factory()->create([
            'user_id' => $admin->id,
            'status' => 'published',
        ]);

        Article::factory()->create([
            'user_id' => $admin->id,
            'status' => 'draft',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            '/api/v1/articles?status=invalid'
        );

        $response
            ->assertStatus(200)
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.total', 0);
    }

    private function createAdmin(): User
    {
        $role = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrator']
        );

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

    private function createAuthor(): User
    {
        $role = Role::firstOrCreate(
            ['slug' => 'author'],
            ['name' => 'Author']
        );

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }
}