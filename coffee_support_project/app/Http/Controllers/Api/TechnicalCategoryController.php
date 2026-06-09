<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicalCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TechnicalCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = TechnicalCategory::latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách danh mục kỹ thuật thành công.',
            'data' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse('Chỉ quản trị viên mới được tạo danh mục kỹ thuật.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:technical_categories,name'],
            'slug' => ['nullable', 'string', 'max:180', 'unique:technical_categories,slug'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);

        $category = TechnicalCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo danh mục kỹ thuật thành công.',
            'data' => $category,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $category = TechnicalCategory::find($id);

        if (!$category) {
            return $this->notFoundResponse('Không tìm thấy danh mục kỹ thuật.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết danh mục kỹ thuật thành công.',
            'data' => $category,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse('Chỉ quản trị viên mới được cập nhật danh mục kỹ thuật.');
        }

        $category = TechnicalCategory::find($id);

        if (!$category) {
            return $this->notFoundResponse('Không tìm thấy danh mục kỹ thuật.');
        }

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:150',
                Rule::unique('technical_categories', 'name')->ignore($category->id),
            ],
            'slug' => [
                'sometimes',
                'nullable',
                'string',
                'max:180',
                Rule::unique('technical_categories', 'slug')->ignore($category->id),
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive'])],
        ]);

        if (isset($validated['name']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật danh mục kỹ thuật thành công.',
            'data' => $category->fresh(),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse('Chỉ quản trị viên mới được xóa danh mục kỹ thuật.');
        }

        $category = TechnicalCategory::withCount('articles')->find($id);

        if (!$category) {
            return $this->notFoundResponse('Không tìm thấy danh mục kỹ thuật.');
        }

        if ($category->articles_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa danh mục vì đang có bài viết thuộc danh mục này. Bạn có thể chuyển trạng thái sang inactive.',
            ], 409);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa danh mục kỹ thuật thành công.',
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