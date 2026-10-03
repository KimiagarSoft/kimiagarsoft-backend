<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleService
{
    public function list(
        User $user,
        ?string $status = null,
        ?string $sort = null,
        ?string $direction = null,
        ?string $search = null
    ): LengthAwarePaginator {
        $query = Article::query();

        if ($user->isAuthor()) {
            $query->where('user_id', $user->id);
        }

        if ($status !== null) {
            $query->where('status', $status);
        }

        if ($search !== null && $search !== '') {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $sort = $sort ?? 'sort_order';
        $direction = $direction === 'desc' ? 'desc' : 'asc';

        $allowedSorts = [
            'sort_order',
            'title',
            'created_at',
        ];

        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction);
        }

        return $query->paginate(10);
    }

    public function create(array $data): Article
    {
        return Article::create($data);
    }

    public function update(Article $article, array $data): Article
    {
        $article->update($data);

        return $article->refresh();
    }

    public function delete(Article $article): void
    {
        $article->delete();
    }
}

