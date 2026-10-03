<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArticleValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_title_is_required_when_creating_article(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'slug' => 'test-article',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_slug_is_required_when_creating_article(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Test Article',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    public function test_slug_must_be_unique_when_creating_article(): void
    {
        $author = $this->createAuthor();

        Article::factory()->create([
            'user_id' => $author->id,
            'slug' => 'existing-article',
        ]);

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Another Article',
            'slug' => 'existing-article',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    public function test_status_is_required_when_creating_article(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'sort_order' => 0,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_status_must_be_valid_when_creating_article(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'status' => 'invalid',
            'sort_order' => 0,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_sort_order_is_required_when_creating_article(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'status' => 'draft',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['sort_order']);
    }

    public function test_sort_order_must_be_an_integer_when_creating_article(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'status' => 'draft',
            'sort_order' => 'invalid',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['sort_order']);
    }

    public function test_sort_order_cannot_be_negative_when_creating_article(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'status' => 'draft',
            'sort_order' => -1,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['sort_order']);
    }

    public function test_published_at_must_be_a_valid_date_when_creating_article(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'status' => 'draft',
            'sort_order' => 0,
            'published_at' => 'not-a-date',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['published_at']);
    }

    public function test_short_description_is_optional_when_creating_article(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response->assertStatus(201);
    }

    public function test_content_is_optional_when_creating_article(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response->assertStatus(201);
    }

    public function test_title_is_required_when_updating_article(): void
    {
        $author = $this->createAuthor();
        $article = $this->createArticle($author);

        Sanctum::actingAs($author);

        $response = $this->putJson(
            "/api/v1/articles/{$article->id}",
            [
                'slug' => 'updated-article',
                'status' => 'draft',
                'sort_order' => 0,
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    public function test_slug_is_required_when_updating_article(): void
    {
        $author = $this->createAuthor();
        $article = $this->createArticle($author);

        Sanctum::actingAs($author);

        $response = $this->putJson(
            "/api/v1/articles/{$article->id}",
            [
                'title' => 'Updated Article',
                'status' => 'draft',
                'sort_order' => 0,
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    public function test_article_can_keep_its_existing_slug_when_updating(): void
    {
        $author = $this->createAuthor();

        $article = $this->createArticle($author, [
            'slug' => 'existing-article',
        ]);

        Sanctum::actingAs($author);

        $response = $this->putJson(
            "/api/v1/articles/{$article->id}",
            [
                'title' => 'Updated Article',
                'slug' => 'existing-article',
                'status' => 'published',
                'sort_order' => 1,
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.slug', 'existing-article');
    }

    public function test_slug_cannot_belong_to_another_article_when_updating(): void
    {
        $author = $this->createAuthor();

        $article = $this->createArticle($author, [
            'slug' => 'first-article',
        ]);

        $this->createArticle($author, [
            'slug' => 'second-article',
        ]);

        Sanctum::actingAs($author);

        $response = $this->putJson(
            "/api/v1/articles/{$article->id}",
            [
                'title' => 'Updated Article',
                'slug' => 'second-article',
                'status' => 'published',
                'sort_order' => 1,
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    public function test_status_must_be_valid_when_updating_article(): void
    {
        $author = $this->createAuthor();
        $article = $this->createArticle($author);

        Sanctum::actingAs($author);

        $response = $this->putJson(
            "/api/v1/articles/{$article->id}",
            [
                'title' => 'Updated Article',
                'slug' => 'updated-article',
                'status' => 'invalid',
                'sort_order' => 0,
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_sort_order_cannot_be_negative_when_updating_article(): void
    {
        $author = $this->createAuthor();
        $article = $this->createArticle($author);

        Sanctum::actingAs($author);

        $response = $this->putJson(
            "/api/v1/articles/{$article->id}",
            [
                'title' => 'Updated Article',
                'slug' => 'updated-article',
                'status' => 'published',
                'sort_order' => -1,
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['sort_order']);
    }

    public function test_published_at_must_be_a_valid_date_when_updating_article(): void
    {
        $author = $this->createAuthor();
        $article = $this->createArticle($author);

        Sanctum::actingAs($author);

        $response = $this->putJson(
            "/api/v1/articles/{$article->id}",
            [
                'title' => 'Updated Article',
                'slug' => 'updated-article',
                'status' => 'published',
                'sort_order' => 0,
                'published_at' => 'not-a-date',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['published_at']);
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

    private function createArticle(
        User $user,
        array $attributes = []
    ): Article {
        return Article::factory()->create(
            array_merge(
                ['user_id' => $user->id],
                $attributes
            )
        );
    }
}