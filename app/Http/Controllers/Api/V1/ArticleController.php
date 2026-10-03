<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        return ArticleResource::collection(
            $this->articleService->list(
                request()->user(),
                request()->query('status'),
                request()->query('sort'),
                request()->query('direction'),
                request()->query('search')
            )
        );
    }

    public function show(Article $article): ArticleResource
    {
        return new ArticleResource($article);
    }

    public function store(StoreArticleRequest $request): ArticleResource
    {
        $data = $request->validated();

        $data['user_id'] = $request->user()->id;

        $article = $this->articleService->create($data);

        return new ArticleResource($article);
    }

    public function update(
        UpdateArticleRequest $request,
        Article $article
    ): ArticleResource {
        $article = $this->articleService->update(
            $article,
            $request->validated()
        );

        return new ArticleResource($article);
    }

    public function destroy(Article $article): Response
    {
        $this->articleService->delete($article);

        return response()->noContent();
    }
}

