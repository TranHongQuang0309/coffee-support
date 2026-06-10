<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoffeeFarm;
use App\Models\DiagnosisRequest;
use App\Models\Disease;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiagnosisRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'diagnosed', 'rejected'])],
            'coffee_farm_id' => ['nullable', 'integer', 'exists:coffee_farms,id'],
            'predicted_disease_id' => ['nullable', 'integer', 'exists:diseases,id'],
        ]);

        $query = DiagnosisRequest::with([
            'user',
            'coffeeFarm',
            'predictedDisease',
            'diagnosedBy',
        ])->latest();

        if ($this->isFarmer($request)) {
            $query->where('user_id', $request->user()->id);

            if (!empty($validated['coffee_farm_id'])) {
                $farm = $this->findOwnedFarm($request, (int) $validated['coffee_farm_id']);

                if (!$farm) {
                    return $this->notFoundResponse('Không tìm thấy vườn cà phê hoặc bạn không có quyền truy cập.');
                }

                $query->where('coffee_farm_id', $farm->id);
            }
        }

        if ($this->isExpert($request) || $this->isAdmin($request)) {
            if (!empty($validated['coffee_farm_id'])) {
                $query->where('coffee_farm_id', $validated['coffee_farm_id']);
            }
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['predicted_disease_id'])) {
            $query->where('predicted_disease_id', $validated['predicted_disease_id']);
        }

        $diagnosisRequests = $query->get();

        return $this->successResponse(
            'Lấy danh sách yêu cầu chẩn đoán thành công.',
            $diagnosisRequests
        );
    }

    public function store(Request $request): JsonResponse
    {
        if (!$this->isFarmer($request)) {
            return $this->forbiddenResponse('Chỉ người trồng cà phê mới được tạo yêu cầu chẩn đoán.');
        }

        $validated = $request->validate([
            'coffee_farm_id' => ['required', 'integer', 'exists:coffee_farms,id'],
            'image_url' => ['required', 'string', 'max:255'],
            'symptom_description' => ['nullable', 'string'],
        ]);

        $farm = $this->findOwnedFarm($request, (int) $validated['coffee_farm_id']);

        if (!$farm) {
            return $this->notFoundResponse('Không tìm thấy vườn cà phê hoặc bạn không có quyền tạo yêu cầu chẩn đoán cho vườn này.');
        }

        $diagnosisRequest = DiagnosisRequest::create([
            'user_id' => $request->user()->id,
            'coffee_farm_id' => $farm->id,
            'predicted_disease_id' => null,
            'diagnosed_by' => null,
            'image_url' => $validated['image_url'],
            'symptom_description' => $validated['symptom_description'] ?? null,
            'confidence_score' => null,
            'diagnosis_note' => null,
            'recommendation' => null,
            'status' => 'pending',
            'diagnosed_at' => null,
        ]);

        $diagnosisRequest->load([
            'user',
            'coffeeFarm',
            'predictedDisease',
            'diagnosedBy',
        ]);

        return $this->successResponse(
            'Tạo yêu cầu chẩn đoán thành công.',
            $diagnosisRequest,
            201
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $diagnosisRequest = DiagnosisRequest::with([
            'user',
            'coffeeFarm',
            'predictedDisease',
            'diagnosedBy',
        ])->find($id);

        if (!$diagnosisRequest) {
            return $this->notFoundResponse('Không tìm thấy yêu cầu chẩn đoán.');
        }

        if (
            $this->isFarmer($request)
            && $diagnosisRequest->user_id !== $request->user()->id
        ) {
            return $this->notFoundResponse('Không tìm thấy yêu cầu chẩn đoán hoặc bạn không có quyền truy cập.');
        }

        return $this->successResponse(
            'Lấy chi tiết yêu cầu chẩn đoán thành công.',
            $diagnosisRequest
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $diagnosisRequest = DiagnosisRequest::find($id);

        if (!$diagnosisRequest) {
            return $this->notFoundResponse('Không tìm thấy yêu cầu chẩn đoán.');
        }

        if ($this->isFarmer($request)) {
            return $this->updateByFarmer($request, $diagnosisRequest);
        }

        if ($this->isExpert($request) || $this->isAdmin($request)) {
            return $this->updateDiagnosisResult($request, $diagnosisRequest);
        }

        return $this->forbiddenResponse('Bạn không có quyền cập nhật yêu cầu chẩn đoán.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $diagnosisRequest = DiagnosisRequest::find($id);

        if (!$diagnosisRequest) {
            return $this->notFoundResponse('Không tìm thấy yêu cầu chẩn đoán.');
        }

        if ($this->isFarmer($request)) {
            if ($diagnosisRequest->user_id !== $request->user()->id) {
                return $this->notFoundResponse('Không tìm thấy yêu cầu chẩn đoán hoặc bạn không có quyền xóa.');
            }

            if ($diagnosisRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể xóa yêu cầu chẩn đoán khi đang chờ xử lý.',
                ], 409);
            }

            $diagnosisRequest->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa yêu cầu chẩn đoán thành công.',
            ]);
        }

        if ($this->isAdmin($request)) {
            $diagnosisRequest->delete();

            return response()->json([
                'success' => true,
                'message' => 'Quản trị viên đã xóa yêu cầu chẩn đoán thành công.',
            ]);
        }

        return $this->forbiddenResponse('Bạn không có quyền xóa yêu cầu chẩn đoán.');
    }

    private function updateByFarmer(Request $request, DiagnosisRequest $diagnosisRequest): JsonResponse
    {
        if ($diagnosisRequest->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Không tìm thấy yêu cầu chẩn đoán hoặc bạn không có quyền cập nhật.');
        }

        if ($diagnosisRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể cập nhật yêu cầu chẩn đoán khi đang chờ xử lý.',
            ], 409);
        }

        $validated = $request->validate([
            'coffee_farm_id' => ['sometimes', 'required', 'integer', 'exists:coffee_farms,id'],
            'image_url' => ['sometimes', 'required', 'string', 'max:255'],
            'symptom_description' => ['sometimes', 'nullable', 'string'],
        ]);

        if (isset($validated['coffee_farm_id'])) {
            $farm = $this->findOwnedFarm($request, (int) $validated['coffee_farm_id']);

            if (!$farm) {
                return $this->notFoundResponse('Không tìm thấy vườn cà phê hoặc bạn không có quyền chuyển yêu cầu sang vườn này.');
            }
        }

        $diagnosisRequest->update($validated);

        return $this->successResponse(
            'Cập nhật yêu cầu chẩn đoán thành công.',
            $diagnosisRequest->fresh([
                'user',
                'coffeeFarm',
                'predictedDisease',
                'diagnosedBy',
            ])
        );
    }

    private function updateDiagnosisResult(Request $request, DiagnosisRequest $diagnosisRequest): JsonResponse
    {
        $validated = $request->validate([
            'predicted_disease_id' => ['nullable', 'integer', 'exists:diseases,id'],
            'confidence_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'diagnosis_note' => ['nullable', 'string'],
            'recommendation' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['pending', 'diagnosed', 'rejected'])],
        ]);

        if (!empty($validated['predicted_disease_id'])) {
            $disease = Disease::where('id', $validated['predicted_disease_id'])
                ->where('status', 'active')
                ->first();

            if (!$disease) {
                return $this->notFoundResponse('Không tìm thấy bệnh/sâu hại đang hoạt động để gắn kết quả chẩn đoán.');
            }
        }

        if ($validated['status'] === 'diagnosed' && empty($validated['predicted_disease_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn bệnh/sâu hại dự đoán khi xác nhận đã chẩn đoán.',
                'errors' => [
                    'predicted_disease_id' => [
                        'Vui lòng chọn bệnh/sâu hại dự đoán.',
                    ],
                ],
            ], 422);
        }

        $diagnosisRequest->update([
            'predicted_disease_id' => $validated['predicted_disease_id'] ?? null,
            'diagnosed_by' => $request->user()->id,
            'confidence_score' => $validated['confidence_score'] ?? null,
            'diagnosis_note' => $validated['diagnosis_note'] ?? null,
            'recommendation' => $validated['recommendation'] ?? null,
            'status' => $validated['status'],
            'diagnosed_at' => in_array($validated['status'], ['diagnosed', 'rejected'], true)
                ? now()
                : null,
        ]);

        return $this->successResponse(
            'Cập nhật kết quả chẩn đoán thành công.',
            $diagnosisRequest->fresh([
                'user',
                'coffeeFarm',
                'predictedDisease',
                'diagnosedBy',
            ])
        );
    }

    private function findOwnedFarm(Request $request, int $farmId): ?CoffeeFarm
    {
        return CoffeeFarm::where('id', $farmId)
            ->where('user_id', $request->user()->id)
            ->first();
    }

    private function isFarmer(Request $request): bool
    {
        return $request->user() && $request->user()->role === 'farmer';
    }

    private function isExpert(Request $request): bool
    {
        return $request->user() && $request->user()->role === 'expert';
    }

    private function isAdmin(Request $request): bool
    {
        return $request->user() && $request->user()->role === 'admin';
    }

    private function successResponse(string $message, mixed $data = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
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