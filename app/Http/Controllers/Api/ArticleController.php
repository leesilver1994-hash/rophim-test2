<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Article::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(12);

        return response()->json($items);
    }

    public function store(StoreArticleRequest $request): JsonResponse
    {
        $data = $request->validated();

        $article = Article::query()->create([
            'title' => $data['title'],
            'slug' => Str::slug($data['title']) . '-' . Str::lower(Str::random(6)),
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'is_published' => $data['is_published'] ?? false,
            'published_at' => ($data['is_published'] ?? false) ? now() : null,
        ]);

        return response()->json($article, 201);
    }
}
