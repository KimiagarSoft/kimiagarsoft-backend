<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(5),
            'slug' => fake()->unique()->slug(),
            'short_description' => fake()->sentence(),
            'content' => fake()->paragraphs(4, true),
            'status' => 'draft',
            'sort_order' => fake()->numberBetween(0, 100),
            'published_at' => null,
        ];
    }
}