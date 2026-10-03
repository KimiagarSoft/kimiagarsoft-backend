<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArticleSearchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_articles_can_be_searched_by_title(): void
    {
        $admin = $this->createAdmin();

        Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'Laravel API Development',
            'slug' => 'laravel-api-development',
        ]);

        Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'WordPress Performance',
            'slug' => 'wordpress-performance',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            '/api/v1/articles?search=Laravel'
        );

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Laravel API Development')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_articles_can_be_searched_by_slug(): void
    {
        $admin = $this->createAdmin();

        Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'API Development',
            'slug' => 'laravel-api-development',
        ]);

        Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'WordPress Performance',
            'slug' => 'wordpress-performance',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            '/api/v1/articles?search=laravel-api'
        );

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'laravel-api-development')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_author_search_only_returns_own_articles(): void
    {
        $author = $this->createAuthor();
        $otherAuthor = $this->createAuthor();

        Article::factory()->create([
            'user_id' => $author->id,
            'title' => 'Laravel My Article',
            'slug' => 'laravel-my-article',
        ]);

        Article::factory()->create([
            'user_id' => $otherAuthor->id,
            'title' => 'Laravel Other Article',
            'slug' => 'laravel-other-article',
        ]);

        Sanctum::actingAs($author);

        $response = $this->getJson(
            '/api/v1/articles?search=Laravel'
        );

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.user_id', $author->id)
            ->assertJsonPath('data.0.title', 'Laravel My Article')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_search_with_no_match_returns_empty_result(): void
    {
        $admin = $this->createAdmin();

        Article::factory()->create([
            'user_id' => $admin->id,
            'title' => 'Laravel API Development',
            'slug' => 'laravel-api-development',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson(
            '/api/v1/articles?search=VueJS'
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

