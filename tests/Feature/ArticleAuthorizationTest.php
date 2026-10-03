<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArticleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_list_articles(): void
    {
        $response = $this->getJson('/api/v1/articles');

        $response->assertStatus(401);
    }

    public function test_guest_cannot_view_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->getJson("/api/v1/articles/{$article->id}");

        $response->assertStatus(401);
    }

    public function test_guest_cannot_create_article(): void
    {
        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Test Article',
            'slug' => 'test-article',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response->assertStatus(401);
    }

    public function test_guest_cannot_update_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->putJson(
            "/api/v1/articles/{$article->id}",
            [
                'title' => 'Updated Article',
                'slug' => 'updated-article',
                'status' => 'draft',
                'sort_order' => 0,
            ]
        );

        $response->assertStatus(401);
    }

    public function test_guest_cannot_delete_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->deleteJson(
            "/api/v1/articles/{$article->id}"
        );

        $response->assertStatus(401);
    }

    public function test_admin_can_list_articles(): void
    {
        $admin = $this->createAdmin();

        Article::factory()->count(2)->create();

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/articles');

        $response
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_author_can_list_articles(): void
    {
        $author = $this->createAuthor();

        Article::factory()->create([
            'user_id' => $author->id,
        ]);

        Sanctum::actingAs($author);

        $response = $this->getJson('/api/v1/articles');

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_author_only_sees_own_articles_in_list(): void
    {
        $author = $this->createAuthor();
        $otherAuthor = $this->createAuthor();

        Article::factory()->create([
            'user_id' => $author->id,
            'title' => 'Own Article',
        ]);

        Article::factory()->create([
            'user_id' => $otherAuthor->id,
            'title' => 'Other Article',
        ]);

        Sanctum::actingAs($author);

        $response = $this->getJson('/api/v1/articles');

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Own Article');
    }

    public function test_admin_can_view_any_article(): void
    {
        $admin = $this->createAdmin();
        $article = Article::factory()->create();

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            "/api/v1/articles/{$article->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.id', $article->id);
    }

    public function test_author_can_view_own_article(): void
    {
        $author = $this->createAuthor();

        $article = Article::factory()->create([
            'user_id' => $author->id,
        ]);

        Sanctum::actingAs($author);

        $response = $this->getJson(
            "/api/v1/articles/{$article->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.id', $article->id);
    }

    public function test_author_cannot_view_another_authors_article(): void
    {
        $author = $this->createAuthor();
        $otherAuthor = $this->createAuthor();

        $article = Article::factory()->create([
            'user_id' => $otherAuthor->id,
        ]);

        Sanctum::actingAs($author);

        $response = $this->getJson(
            "/api/v1/articles/{$article->id}"
        );

        $response->assertStatus(403);
    }

    public function test_admin_can_create_article(): void
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Admin Article',
            'slug' => 'admin-article',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.title', 'Admin Article')
            ->assertJsonPath('data.user_id', $admin->id);
    }

    public function test_author_can_create_article(): void
    {
        $author = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Author Article',
            'slug' => 'author-article',
            'status' => 'draft',
            'sort_order' => 0,
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.title', 'Author Article')
            ->assertJsonPath('data.user_id', $author->id);
    }

    public function test_author_cannot_assign_article_to_another_user(): void
    {
        $author = $this->createAuthor();
        $otherAuthor = $this->createAuthor();

        Sanctum::actingAs($author);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Author Article',
            'slug' => 'author-article',
            'status' => 'draft',
            'sort_order' => 0,
            'user_id' => $otherAuthor->id,
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.user_id', $author->id);
    }

    public function test_admin_can_update_any_article(): void
    {
        $admin = $this->createAdmin();
        $article = Article::factory()->create();

        Sanctum::actingAs($admin);

        $response = $this->putJson(
            "/api/v1/articles/{$article->id}",
            [
                'title' => 'Updated Article',
                'slug' => 'updated-article',
                'status' => 'published',
                'sort_order' => 1,
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Article');
    }

    public function test_author_can_update_own_article(): void
    {
        $author = $this->createAuthor();

        $article = Article::factory()->create([
            'user_id' => $author->id,
        ]);

        Sanctum::actingAs($author);

        $response = $this->putJson(
            "/api/v1/articles/{$article->id}",
            [
                'title' => 'Updated Article',
                'slug' => 'updated-article',
                'status' => 'published',
                'sort_order' => 1,
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Article');
    }

    public function test_author_cannot_update_another_authors_article(): void
    {
        $author = $this->createAuthor();
        $otherAuthor = $this->createAuthor();

        $article = Article::factory()->create([
            'user_id' => $otherAuthor->id,
        ]);

        Sanctum::actingAs($author);

        $response = $this->putJson(
            "/api/v1/articles/{$article->id}",
            [
                'title' => 'Updated Article',
                'slug' => 'updated-article',
                'status' => 'published',
                'sort_order' => 1,
            ]
        );

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_any_article(): void
    {
        $admin = $this->createAdmin();
        $article = Article::factory()->create();

        Sanctum::actingAs($admin);

        $response = $this->deleteJson(
            "/api/v1/articles/{$article->id}"
        );

        $response->assertStatus(204);

        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
        ]);
    }

    public function test_author_cannot_delete_article(): void
    {
        $author = $this->createAuthor();

        $article = Article::factory()->create([
            'user_id' => $author->id,
        ]);

        Sanctum::actingAs($author);

        $response = $this->deleteJson(
            "/api/v1/articles/{$article->id}"
        );

        $response->assertStatus(403);
    }

    public function test_author_cannot_delete_another_authors_article(): void
    {
        $author = $this->createAuthor();
        $otherAuthor = $this->createAuthor();

        $article = Article::factory()->create([
            'user_id' => $otherAuthor->id,
        ]);

        Sanctum::actingAs($author);

        $response = $this->deleteJson(
            "/api/v1/articles/{$article->id}"
        );

        $response->assertStatus(403);
    }

    public function test_admin_can_create_article_with_published_status(): void
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/articles', [
            'title' => 'Published Article',
            'slug' => 'published-article',
            'status' => 'published',
            'sort_order' => 0,
            'published_at' => now()->toDateTimeString(),
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.status', 'published');
    }

    public function test_author_can_update_own_article_without_changing_owner(): void
    {
        $author = $this->createAuthor();

        $article = Article::factory()->create([
            'user_id' => $author->id,
        ]);

        Sanctum::actingAs($author);

        $response = $this->putJson(
            "/api/v1/articles/{$article->id}",
            [
                'title' => 'Updated Article',
                'slug' => 'updated-article',
                'status' => 'published',
                'sort_order' => 1,
                'user_id' => $this->createAuthor()->id,
            ]
        );

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.user_id', $author->id);
    }

    public function test_author_cannot_update_owner_of_another_authors_article(): void
    {
        $author = $this->createAuthor();
        $otherAuthor = $this->createAuthor();

        $article = Article::factory()->create([
            'user_id' => $otherAuthor->id,
        ]);

        Sanctum::actingAs($author);

        $response = $this->putJson(
            "/api/v1/articles/{$article->id}",
            [
                'title' => 'Updated Article',
                'slug' => 'updated-article',
                'status' => 'published',
                'sort_order' => 1,
                'user_id' => $author->id,
            ]
        );

        $response->assertStatus(403);
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