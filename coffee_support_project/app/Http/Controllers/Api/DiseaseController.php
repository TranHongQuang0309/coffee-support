<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Disease;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DiseaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => [
                'nullable',
                Rule::in([
                    'disease',
                    'pest',
                    'nutrient_deficiency',
                    'other',
                ]),
            ],
            'severity' => [
                'nullable',
                Rule::in([
                    'low',
                    'medium',
                    'high',
                ]),
            ],
            'status' => [
                'nullable',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
            'keyword' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Disease::with('creator')
            ->latest();

        if (!empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        if (!empty($validated['severity'])) {
            $query->where('severity', $validated['severity']);
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['keyword'])) {
            $keyword = $validated['keyword'];

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('symptoms', 'like', "%{$keyword}%")
                    ->orWhere('causes', 'like', "%{$keyword}%")
                    ->orWhere('prevention', 'like', "%{$keyword}%")
                    ->orWhere('treatment', 'like', "%{$keyword}%");
            });
        }

        $diseases = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách sâu bệnh thành công.',
            'data' => $diseases,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse('Chỉ quản trị viên mới được tạo dữ liệu sâu bệnh.');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'unique:diseases,name',
            ],
            'slug' => [
                'nullable',
                'string',
                'max:180',
                'unique:diseases,slug',
            ],
            'type' => [
                'required',
                Rule::in([
                    'disease',
                    'pest',
                    'nutrient_deficiency',
                    'other',
                ]),
            ],
            'symptoms' => ['required', 'string'],
            'causes' => ['nullable', 'string'],
            'prevention' => ['nullable', 'string'],
            'treatment' => ['nullable', 'string'],
            'severity' => [
                'required',
                Rule::in([
                    'low',
                    'medium',
                    'high',
                ]),
            ],
            'image_url' => ['nullable', 'string', 'max:255'],
            'status' => [
                'nullable',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);

        $disease = Disease::create([
            'created_by' => $request->user()->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'type' => $validated['type'],
            'symptoms' => $validated['symptoms'],
            'causes' => $validated['causes'] ?? null,
            'prevention' => $validated['prevention'] ?? null,
            'treatment' => $validated['treatment'] ?? null,
            'severity' => $validated['severity'],
            'image_url' => $validated['image_url'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        $disease->load('creator');

        return response()->json([
            'success' => true,
            'message' => 'Tạo dữ liệu sâu bệnh thành công.',
            'data' => $disease,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $disease = Disease::with('creator')->find($id);

        if (!$disease) {
            return $this->notFoundResponse('Không tìm thấy dữ liệu sâu bệnh.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết sâu bệnh thành công.',
            'data' => $disease,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse('Chỉ quản trị viên mới được cập nhật dữ liệu sâu bệnh.');
        }

        $disease = Disease::find($id);

        if (!$disease) {
            return $this->notFoundResponse('Không tìm thấy dữ liệu sâu bệnh.');
        }

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:150',
                Rule::unique('diseases', 'name')->ignore($disease->id),
            ],
            'slug' => [
                'sometimes',
                'nullable',
                'string',
                'max:180',
                Rule::unique('diseases', 'slug')->ignore($disease->id),
            ],
            'type' => [
                'sometimes',
                'required',
                Rule::in([
                    'disease',
                    'pest',
                    'nutrient_deficiency',
                    'other',
                ]),
            ],
            'symptoms' => ['sometimes', 'required', 'string'],
            'causes' => ['sometimes', 'nullable', 'string'],
            'prevention' => ['sometimes', 'nullable', 'string'],
            'treatment' => ['sometimes', 'nullable', 'string'],
            'severity' => [
                'sometimes',
                'required',
                Rule::in([
                    'low',
                    'medium',
                    'high',
                ]),
            ],
            'image_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => [
                'sometimes',
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],
        ]);

        if (isset($validated['name']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $disease->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật dữ liệu sâu bệnh thành công.',
            'data' => $disease->fresh('creator'),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse('Chỉ quản trị viên mới được xóa dữ liệu sâu bệnh.');
        }

        $disease = Disease::withCount('diagnosisRequests')->find($id);

        if (!$disease) {
            return $this->notFoundResponse('Không tìm thấy dữ liệu sâu bệnh.');
        }

        if ($disease->diagnosis_requests_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa sâu bệnh này vì đã được sử dụng trong yêu cầu chẩn đoán. Bạn có thể chuyển trạng thái sang inactive.',
            ], 409);
        }

        $disease->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa dữ liệu sâu bệnh thành công.',
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