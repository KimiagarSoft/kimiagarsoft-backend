<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArticleSortingTest extends TestCase
{
    use RefreshDatabase;

    public function test_articles_can_be_sorted_by_sort_order_ascending(): void
    {
        $admin = $this->createAdmin();

        Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'Article 3',
            'sort_order' => 30,
        ]);

        Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'Article 1',
            'sort_order' => 10,
        ]);

        Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'Article 2',
            'sort_order' => 20,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            '/api/v1/articles?sort=sort_order&direction=asc'
        );

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.0.title', 'Article 1')
            ->assertJsonPath('data.1.title', 'Article 2')
            ->assertJsonPath('data.2.title', 'Article 3');
    }

    public function test_articles_can_be_sorted_by_sort_order_descending(): void
    {
        $admin = $this->createAdmin();

        Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'Article 3',
            'sort_order' => 30,
        ]);

        Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'Article 1',
            'sort_order' => 10,
        ]);

        Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'Article 2',
            'sort_order' => 20,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            '/api/v1/articles?sort=sort_order&direction=desc'
        );

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.0.title', 'Article 3')
            ->assertJsonPath('data.1.title', 'Article 2')
            ->assertJsonPath('data.2.title', 'Article 1');
    }

    public function test_author_sorting_only_returns_own_articles(): void
    {
        $author = $this->createAuthor();
        $otherAuthor = $this->createAuthor();

        Article::factory()->create([
            'user_id' => $author->id,
            'title' => 'My Article 2',
            'sort_order' => 20,
        ]);

        Article::factory()->create([
            'user_id' => $author->id,
            'title' => 'My Article 1',
            'sort_order' => 10,
        ]);

        Article::factory()->create([
            'user_id' => $otherAuthor->id,
            'title' => 'Other Article',
            'sort_order' => 1,
        ]);

        Sanctum::actingAs($author);

        $response = $this->getJson(
            '/api/v1/articles?sort=sort_order&direction=asc'
        );

        $response
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.user_id', $author->id)
            ->assertJsonPath('data.0.title', 'My Article 1')
            ->assertJsonPath('data.1.title', 'My Article 2');
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