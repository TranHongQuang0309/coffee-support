<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicalArticle;
use App\Models\TechnicalCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TechnicalArticleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TechnicalArticle::with(['category', 'creator'])
            ->latest();

        if ($request->filled('technical_category_id')) {
            $query->where('technical_category_id', $request->technical_category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('summary', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        }

        $articles = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách bài viết kỹ thuật thành công.',
            'data' => $articles,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse('Chỉ quản trị viên mới được tạo bài viết kỹ thuật.');
        }

        $validated = $request->validate([
            'technical_category_id' => [
                'required',
                'integer',
                'exists:technical_categories,id',
            ],
            'title' => [
                'required',
                'string',
                'max:200',
                'unique:technical_articles,title',
            ],
            'slug' => [
                'nullable',
                'string',
                'max:220',
                'unique:technical_articles,slug',
            ],
            'summary' => [
                'nullable',
                'string',
                'max:500',
            ],
            'content' => [
                'required',
                'string',
            ],
            'thumbnail_url' => [
                'nullable',
                'string',
                'max:255',
            ],
            'status' => [
                'nullable',
                Rule::in(['draft', 'published', 'hidden']),
            ],
            'published_at' => [
                'nullable',
                'date',
            ],
        ]);

        $category = TechnicalCategory::find($validated['technical_category_id']);

        if (!$category) {
            return $this->notFoundResponse('Không tìm thấy danh mục kỹ thuật.');
        }

        $slug = $validated['slug'] ?? Str::slug($validated['title']);

        $article = TechnicalArticle::create([
            'technical_category_id' => $validated['technical_category_id'],
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'],
            'thumbnail_url' => $validated['thumbnail_url'] ?? null,
            'status' => $validated['status'] ?? 'draft',
            'view_count' => 0,
            'published_at' => $validated['published_at'] ?? null,
        ]);

        $article->load(['category', 'creator']);

        return response()->json([
            'success' => true,
            'message' => 'Tạo bài viết kỹ thuật thành công.',
            'data' => $article,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $article = TechnicalArticle::with(['category', 'creator'])->find($id);

        if (!$article) {
            return $this->notFoundResponse('Không tìm thấy bài viết kỹ thuật.');
        }

        $article->increment('view_count');

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết bài viết kỹ thuật thành công.',
            'data' => $article->fresh(['category', 'creator']),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse('Chỉ quản trị viên mới được cập nhật bài viết kỹ thuật.');
        }

        $article = TechnicalArticle::find($id);

        if (!$article) {
            return $this->notFoundResponse('Không tìm thấy bài viết kỹ thuật.');
        }

        $validated = $request->validate([
            'technical_category_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:technical_categories,id',
            ],
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:200',
                Rule::unique('technical_articles', 'title')->ignore($article->id),
            ],
            'slug' => [
                'sometimes',
                'nullable',
                'string',
                'max:220',
                Rule::unique('technical_articles', 'slug')->ignore($article->id),
            ],
            'summary' => [
                'sometimes',
                'nullable',
                'string',
                'max:500',
            ],
            'content' => [
                'sometimes',
                'required',
                'string',
            ],
            'thumbnail_url' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            'status' => [
                'sometimes',
                'required',
                Rule::in(['draft', 'published', 'hidden']),
            ],
            'published_at' => [
                'sometimes',
                'nullable',
                'date',
            ],
        ]);

        if (isset($validated['title']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (($validated['status'] ?? null) === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật bài viết kỹ thuật thành công.',
            'data' => $article->fresh(['category', 'creator']),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse('Chỉ quản trị viên mới được xóa bài viết kỹ thuật.');
        }

        $article = TechnicalArticle::find($id);

        if (!$article) {
            return $this->notFoundResponse('Không tìm thấy bài viết kỹ thuật.');
        }

        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa bài viết kỹ thuật thành công.',
        ]);
    }

    private function isAdmin(Request $request): bool
    {
        return $request->user() && $request->user()->role === 'admin';
    }

    private function forbiddenResponse(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }

    private function notFoundResponse(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 404);
    }
}