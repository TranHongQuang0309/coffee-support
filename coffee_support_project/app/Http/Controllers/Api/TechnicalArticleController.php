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
        $validated = $request->validate([
            'technical_category_id' => ['nullable', 'integer', 'exists:technical_categories,id'],
            'status' => ['nullable', Rule::in(['draft', 'published', 'hidden'])],
            'keyword' => ['nullable', 'string', 'max:255'],
            'source_type' => [
                'nullable',
                Rule::in([
                    'self_written',
                    'external',
                    'summarized',
                    'expert_contributed',
                ]),
            ],
            'is_verified' => ['nullable', 'boolean'],
        ]);

        $query = TechnicalArticle::with(['category', 'creator', 'verifier'])
            ->latest();

        if (!empty($validated['technical_category_id'])) {
            $query->where('technical_category_id', $validated['technical_category_id']);
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['source_type'])) {
            $query->where('source_type', $validated['source_type']);
        }

        if (array_key_exists('is_verified', $validated)) {
            $query->where('is_verified', $validated['is_verified']);
        }

        if (!empty($validated['keyword'])) {
            $keyword = $validated['keyword'];

            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('summary', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%")
                    ->orWhere('source_name', 'like', "%{$keyword}%");
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

            'source_type' => [
                'nullable',
                Rule::in([
                    'self_written',
                    'external',
                    'summarized',
                    'expert_contributed',
                ]),
            ],
            'source_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'source_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            'is_verified' => [
                'nullable',
                'boolean',
            ],
        ]);

        $category = TechnicalCategory::find($validated['technical_category_id']);

        if (!$category) {
            return $this->notFoundResponse('Không tìm thấy danh mục kỹ thuật.');
        }

        $sourceType = $validated['source_type'] ?? 'self_written';

        if (in_array($sourceType, ['external', 'summarized'], true) && empty($validated['source_name'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập tên nguồn tham khảo cho bài viết lấy từ nguồn ngoài hoặc tổng hợp từ nhiều nguồn.',
                'errors' => [
                    'source_name' => [
                        'Vui lòng nhập tên nguồn tham khảo.',
                    ],
                ],
            ], 422);
        }

        $status = $validated['status'] ?? 'draft';
        $slug = $validated['slug'] ?? Str::slug($validated['title']);
        $isVerified = $validated['is_verified'] ?? false;

        $article = TechnicalArticle::create([
            'technical_category_id' => $validated['technical_category_id'],
            'created_by' => $request->user()->id,
            'title' => $validated['title'],
            'slug' => $slug,
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'],
            'thumbnail_url' => $validated['thumbnail_url'] ?? null,
            'status' => $status,
            'view_count' => 0,
            'published_at' => $status === 'published'
                ? ($validated['published_at'] ?? now())
                : ($validated['published_at'] ?? null),

            'source_type' => $sourceType,
            'source_name' => $validated['source_name'] ?? null,
            'source_url' => $validated['source_url'] ?? null,
            'is_verified' => $isVerified,
            'verified_by' => $isVerified ? $request->user()->id : null,
            'verified_at' => $isVerified ? now() : null,
        ]);

        $article->load(['category', 'creator', 'verifier']);

        return response()->json([
            'success' => true,
            'message' => 'Tạo bài viết kỹ thuật thành công.',
            'data' => $article,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $article = TechnicalArticle::with(['category', 'creator', 'verifier'])->find($id);

        if (!$article) {
            return $this->notFoundResponse('Không tìm thấy bài viết kỹ thuật.');
        }

        $article->increment('view_count');

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết bài viết kỹ thuật thành công.',
            'data' => $article->fresh(['category', 'creator', 'verifier']),
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

            'source_type' => [
                'sometimes',
                'required',
                Rule::in([
                    'self_written',
                    'external',
                    'summarized',
                    'expert_contributed',
                ]),
            ],
            'source_name' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            'source_url' => [
                'sometimes',
                'nullable',
                'url',
                'max:500',
            ],
            'is_verified' => [
                'sometimes',
                'required',
                'boolean',
            ],
        ]);

        $sourceType = $validated['source_type'] ?? $article->source_type;

        if (in_array($sourceType, ['external', 'summarized'], true)) {
            $sourceName = $validated['source_name'] ?? $article->source_name;

            if (empty($sourceName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập tên nguồn tham khảo cho bài viết lấy từ nguồn ngoài hoặc tổng hợp từ nhiều nguồn.',
                    'errors' => [
                        'source_name' => [
                            'Vui lòng nhập tên nguồn tham khảo.',
                        ],
                    ],
                ], 422);
            }
        }

        if (isset($validated['title']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (
            ($validated['status'] ?? null) === 'published'
            && empty($validated['published_at'])
            && !$article->published_at
        ) {
            $validated['published_at'] = now();
        }

        if (array_key_exists('is_verified', $validated)) {
            if ($validated['is_verified']) {
                $validated['verified_by'] = $request->user()->id;
                $validated['verified_at'] = now();
            } else {
                $validated['verified_by'] = null;
                $validated['verified_at'] = null;
            }
        }

        $article->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật bài viết kỹ thuật thành công.',
            'data' => $article->fresh(['category', 'creator', 'verifier']),
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