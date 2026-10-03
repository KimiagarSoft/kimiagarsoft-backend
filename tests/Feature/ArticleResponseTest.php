<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArticleResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_show_response_matches_resource_contract(): void
    {
        $admin = $this->createAdmin();

        $article = Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'Test Article',
            'slug' => 'test-article',
            'short_description' => 'Short description',
            'content' => 'Article content',
            'status' => 'published',
            'sort_order' => 1,
            'published_at' => now(),
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            "/api/v1/articles/{$article->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'title',
                    'slug',
                    'short_description',
                    'content',
                    'status',
                    'sort_order',
                    'published_at',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonPath('data.id', $article->id)
            ->assertJsonPath('data.user_id', $admin->id)
            ->assertJsonPath('data.title', 'Test Article')
            ->assertJsonPath('data.slug', 'test-article')
            ->assertJsonPath(
                'data.short_description',
                'Short description'
            )
            ->assertJsonPath(
                'data.content',
                'Article content'
            )
            ->assertJsonPath('data.status', 'published')
            ->assertJsonPath('data.sort_order', 1);
    }

    public function test_article_list_response_matches_resource_contract(): void
    {
        $admin = $this->createAdmin();

        Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'First Article',
            'slug' => 'first-article',
            'sort_order' => 1,
        ]);

        Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'Second Article',
            'slug' => 'second-article',
            'sort_order' => 2,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/articles');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'title',
                        'slug',
                        'short_description',
                        'content',
                        'status',
                        'sort_order',
                        'published_at',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonPath('data.0.title', 'First Article')
            ->assertJsonPath('data.0.slug', 'first-article')
            ->assertJsonPath('data.1.title', 'Second Article')
            ->assertJsonPath('data.1.slug', 'second-article');
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
}