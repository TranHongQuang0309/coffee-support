<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoffeeFarm;
use App\Models\CultivationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CultivationLogController extends Controller
{
    /**
     * Lấy danh sách nhật ký canh tác của farmer đang đăng nhập.
     */
    public function index(Request $request): JsonResponse
    {
        if (!$this->isFarmer($request)) {
            return $this->forbiddenResponse('Chỉ người trồng cà phê mới được xem nhật ký canh tác.');
        }

        $query = CultivationLog::with('coffeeFarm')
            ->where('user_id', $request->user()->id)
            ->latest('log_date');

        if ($request->filled('coffee_farm_id')) {
            $farm = $this->findOwnedFarm($request, (int) $request->coffee_farm_id);

            if (!$farm) {
                return $this->notFoundResponse('Không tìm thấy vườn cà phê hoặc bạn không có quyền truy cập.');
            }

            $query->where('coffee_farm_id', $farm->id);
        }

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('log_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('log_date', '<=', $request->to_date);
        }

        $logs = $query->get();

        return $this->successResponse(
            'Lấy danh sách nhật ký canh tác thành công.',
            $logs
        );
    }

    /**
     * Tạo nhật ký canh tác mới.
     */
    public function store(Request $request): JsonResponse
    {
        if (!$this->isFarmer($request)) {
            return $this->forbiddenResponse('Chỉ người trồng cà phê mới được tạo nhật ký canh tác.');
        }

        $validated = $request->validate([
            'coffee_farm_id' => ['required', 'integer', 'exists:coffee_farms,id'],
            'log_date' => ['required', 'date'],
            'activity_type' => [
                'required',
                Rule::in([
                    'watering',
                    'fertilizing',
                    'spraying',
                    'pruning',
                    'weeding',
                    'harvesting',
                    'pest_checking',
                    'other',
                ]),
            ],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'status' => [
                'nullable',
                Rule::in([
                    'planned',
                    'completed',
                    'cancelled',
                ]),
            ],
        ]);

        $farm = $this->findOwnedFarm($request, (int) $validated['coffee_farm_id']);

        if (!$farm) {
            return $this->notFoundResponse('Không tìm thấy vườn cà phê hoặc bạn không có quyền tạo nhật ký cho vườn này.');
        }

        $log = CultivationLog::create([
            'user_id' => $request->user()->id,
            'coffee_farm_id' => $farm->id,
            'log_date' => $validated['log_date'],
            'activity_type' => $validated['activity_type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'cost' => $validated['cost'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'status' => $validated['status'] ?? 'completed',
        ]);

        $log->load('coffeeFarm');

        return $this->successResponse(
            'Tạo nhật ký canh tác thành công.',
            $log,
            201
        );
    }

    /**
     * Xem chi tiết nhật ký canh tác.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        if (!$this->isFarmer($request)) {
            return $this->forbiddenResponse('Chỉ người trồng cà phê mới được xem chi tiết nhật ký canh tác.');
        }

        $log = $this->findOwnedLog($request, $id);

        if (!$log) {
            return $this->notFoundResponse('Không tìm thấy nhật ký canh tác hoặc bạn không có quyền truy cập.');
        }

        $log->load('coffeeFarm');

        return $this->successResponse(
            'Lấy chi tiết nhật ký canh tác thành công.',
            $log
        );
    }

    /**
     * Cập nhật nhật ký canh tác.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        if (!$this->isFarmer($request)) {
            return $this->forbiddenResponse('Chỉ người trồng cà phê mới được cập nhật nhật ký canh tác.');
        }

        $log = $this->findOwnedLog($request, $id);

        if (!$log) {
            return $this->notFoundResponse('Không tìm thấy nhật ký canh tác hoặc bạn không có quyền cập nhật.');
        }

        $validated = $request->validate([
            'coffee_farm_id' => ['sometimes', 'required', 'integer', 'exists:coffee_farms,id'],
            'log_date' => ['sometimes', 'required', 'date'],
            'activity_type' => [
                'sometimes',
                'required',
                Rule::in([
                    'watering',
                    'fertilizing',
                    'spraying',
                    'pruning',
                    'weeding',
                    'harvesting',
                    'pest_checking',
                    'other',
                ]),
            ],
            'title' => ['sometimes', 'required', 'string', 'max:150'],
            'description' => ['sometimes', 'nullable', 'string'],
            'cost' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'image_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => [
                'sometimes',
                'required',
                Rule::in([
                    'planned',
                    'completed',
                    'cancelled',
                ]),
            ],
        ]);

        if (isset($validated['coffee_farm_id'])) {
            $farm = $this->findOwnedFarm($request, (int) $validated['coffee_farm_id']);

            if (!$farm) {
                return $this->notFoundResponse('Không tìm thấy vườn cà phê hoặc bạn không có quyền chuyển nhật ký sang vườn này.');
            }
        }

        $log->update($validated);

        $log->load('coffeeFarm');

        return $this->successResponse(
            'Cập nhật nhật ký canh tác thành công.',
            $log->fresh('coffeeFarm')
        );
    }

    /**
     * Xóa nhật ký canh tác.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        if (!$this->isFarmer($request)) {
            return $this->forbiddenResponse('Chỉ người trồng cà phê mới được xóa nhật ký canh tác.');
        }

        $log = $this->findOwnedLog($request, $id);

        if (!$log) {
            return $this->notFoundResponse('Không tìm thấy nhật ký canh tác hoặc bạn không có quyền xóa.');
        }

        $log->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa nhật ký canh tác thành công.',
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
     * Tìm vườn theo id và đảm bảo vườn thuộc farmer đang đăng nhập.
     */
    private function findOwnedFarm(Request $request, int $farmId): ?CoffeeFarm
    {
        return CoffeeFarm::where('id', $farmId)
            ->where('user_id', $request->user()->id)
            ->first();
    }

    /**
     * Tìm nhật ký theo id và đảm bảo nhật ký thuộc farmer đang đăng nhập.
     */
    private function findOwnedLog(Request $request, int $logId): ?CultivationLog
    {
        return CultivationLog::where('id', $logId)
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