<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArticlePaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_list_is_paginated(): void
    {
        $admin = $this->createAdmin();

        Article::factory()
            ->count(15)
            ->create([
                'user_id' => $admin->id,
            ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/articles');

        $response
            ->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.total', 15);
    }

    public function test_article_list_returns_second_page(): void
    {
        $admin = $this->createAdmin();

        Article::factory()
            ->count(15)
            ->create([
                'user_id' => $admin->id,
            ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/articles?page=2');

        $response
            ->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.total', 15);
    }

    public function test_author_pagination_only_counts_own_articles(): void
    {
        $author = $this->createAuthor();
        $otherAuthor = $this->createAuthor();

        Article::factory()
            ->count(12)
            ->create([
                'user_id' => $author->id,
            ]);

        Article::factory()
            ->count(8)
            ->create([
                'user_id' => $otherAuthor->id,
            ]);

        Sanctum::actingAs($author);

        $response = $this->getJson('/api/v1/articles');

        $response
            ->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.total', 12);
    }

    public function test_author_can_access_second_page_of_own_articles(): void
    {
        $author = $this->createAuthor();
        $otherAuthor = $this->createAuthor();

        Article::factory()
            ->count(12)
            ->create([
                'user_id' => $author->id,
            ]);

        Article::factory()
            ->count(8)
            ->create([
                'user_id' => $otherAuthor->id,
            ]);

        Sanctum::actingAs($author);

        $response = $this->getJson('/api/v1/articles?page=2');

        $response
            ->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.total', 12);
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