<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoffeeFarm;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CoffeeFarmController extends Controller
{
    /**
     * Lấy danh sách vườn cà phê của user đang đăng nhập.
     */
    public function index(Request $request): JsonResponse
    {
        if (!$this->isFarmer($request)) {
            return $this->forbiddenResponse('Chỉ người trồng cà phê mới được xem danh sách vườn.');
        }

        $farms = CoffeeFarm::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return $this->successResponse(
            'Lấy danh sách vườn cà phê thành công.',
            $farms
        );
    }

    /**
     * Tạo mới vườn cà phê.
     */
    public function store(Request $request): JsonResponse
    {
        if (!$this->isFarmer($request)) {
            return $this->forbiddenResponse('Chỉ người trồng cà phê mới được tạo vườn.');
        }

        $validated = $request->validate([
            'farm_name' => ['required', 'string', 'max:150'],
            'area' => ['required', 'numeric', 'min:0.01'],
            'coffee_type' => ['required', Rule::in(['robusta', 'arabica', 'mixed', 'other'])],
            'planting_year' => ['nullable', 'integer', 'min:1900', 'max:' . now()->year],

            'province' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],

            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'description' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $farm = CoffeeFarm::create([
            'user_id' => $request->user()->id,
            'farm_name' => $validated['farm_name'],
            'area' => $validated['area'],
            'coffee_type' => $validated['coffee_type'],
            'planting_year' => $validated['planting_year'] ?? null,
            'province' => $validated['province'],
            'district' => $validated['district'] ?? null,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        return $this->successResponse(
            'Tạo vườn cà phê thành công.',
            $farm,
            201
        );
    }

    /**
     * Xem chi tiết một vườn cà phê.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        if (!$this->isFarmer($request)) {
            return $this->forbiddenResponse('Chỉ người trồng cà phê mới được xem chi tiết vườn.');
        }

        $farm = $this->findOwnedFarm($request, $id);

        if (!$farm) {
            return $this->notFoundResponse('Không tìm thấy vườn cà phê hoặc bạn không có quyền truy cập.');
        }

        return $this->successResponse(
            'Lấy chi tiết vườn cà phê thành công.',
            $farm
        );
    }

    /**
     * Cập nhật thông tin vườn cà phê.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        if (!$this->isFarmer($request)) {
            return $this->forbiddenResponse('Chỉ người trồng cà phê mới được cập nhật vườn.');
        }

        $farm = $this->findOwnedFarm($request, $id);

        if (!$farm) {
            return $this->notFoundResponse('Không tìm thấy vườn cà phê hoặc bạn không có quyền cập nhật.');
        }

        $validated = $request->validate([
            'farm_name' => ['sometimes', 'required', 'string', 'max:150'],
            'area' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'coffee_type' => ['sometimes', 'required', Rule::in(['robusta', 'arabica', 'mixed', 'other'])],
            'planting_year' => ['sometimes', 'nullable', 'integer', 'min:1900', 'max:' . now()->year],

            'province' => ['sometimes', 'required', 'string', 'max:100'],
            'district' => ['sometimes', 'nullable', 'string', 'max:100'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],

            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],

            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive'])],
        ]);

        $farm->update($validated);

        return $this->successResponse(
            'Cập nhật vườn cà phê thành công.',
            $farm->fresh()
        );
    }

    /**
     * Xóa vườn cà phê.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        if (!$this->isFarmer($request)) {
            return $this->forbiddenResponse('Chỉ người trồng cà phê mới được xóa vườn.');
        }

        $farm = $this->findOwnedFarm($request, $id);

        if (!$farm) {
            return $this->notFoundResponse('Không tìm thấy vườn cà phê hoặc bạn không có quyền xóa.');
        }

        $farm->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa vườn cà phê thành công.',
        ]);
    }

    /**
     * Kiểm tra user hiện tại có phải farmer không.
     */
    private function isFarmer(Request $request): bool
    {
        return $request->user() && $request->user()->role === 'farmer';
    }

    /**
     * Tìm vườn theo id và đảm bảo vườn thuộc user đang đăng nhập.
     */
    private function findOwnedFarm(Request $request, int $id): ?CoffeeFarm
    {
        return CoffeeFarm::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();
    }

    /**
     * Response thành công thống nhất.
     */
    private function successResponse(string $message, mixed $data = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Response lỗi 403.
     */
    private function forbiddenResponse(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 403);
    }

    /**
     * Response lỗi 404.
     */
    private function notFoundResponse(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 404);
    }
}