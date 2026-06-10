<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoffeeFarm;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'answered', 'closed'])],
            'coffee_farm_id' => ['nullable', 'integer', 'exists:coffee_farms,id'],
            'keyword' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Question::with(['user', 'coffeeFarm'])
            ->withCount('answers')
            ->latest();

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

        if (!empty($validated['keyword'])) {
            $keyword = $validated['keyword'];

            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        }

        $questions = $query->get();

        return $this->successResponse(
            'Lấy danh sách câu hỏi kỹ thuật thành công.',
            $questions
        );
    }

    public function store(Request $request): JsonResponse
    {
        if (!$this->isFarmer($request)) {
            return $this->forbiddenResponse('Chỉ người trồng cà phê mới được đặt câu hỏi kỹ thuật.');
        }

        $validated = $request->validate([
            'coffee_farm_id' => ['nullable', 'integer', 'exists:coffee_farms,id'],
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'image_url' => ['nullable', 'string', 'max:255'],
        ]);

        $coffeeFarmId = null;

        if (!empty($validated['coffee_farm_id'])) {
            $farm = $this->findOwnedFarm($request, (int) $validated['coffee_farm_id']);

            if (!$farm) {
                return $this->notFoundResponse('Không tìm thấy vườn cà phê hoặc bạn không có quyền đặt câu hỏi cho vườn này.');
            }

            $coffeeFarmId = $farm->id;
        }

        $question = Question::create([
            'user_id' => $request->user()->id,
            'coffee_farm_id' => $coffeeFarmId,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_url' => $validated['image_url'] ?? null,
            'status' => 'pending',
            'view_count' => 0,
        ]);

        $question->load(['user', 'coffeeFarm']);

        return $this->successResponse(
            'Tạo câu hỏi kỹ thuật thành công.',
            $question,
            201
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $question = Question::with(['user', 'coffeeFarm', 'answers.user'])
            ->withCount('answers')
            ->find($id);

        if (!$question) {
            return $this->notFoundResponse('Không tìm thấy câu hỏi kỹ thuật.');
        }

        if (
            $this->isFarmer($request)
            && $question->user_id !== $request->user()->id
        ) {
            return $this->notFoundResponse('Không tìm thấy câu hỏi kỹ thuật hoặc bạn không có quyền truy cập.');
        }

        $question->increment('view_count');

        return $this->successResponse(
            'Lấy chi tiết câu hỏi kỹ thuật thành công.',
            $question->fresh(['user', 'coffeeFarm', 'answers.user'])
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $question = Question::find($id);

        if (!$question) {
            return $this->notFoundResponse('Không tìm thấy câu hỏi kỹ thuật.');
        }

        if ($this->isFarmer($request)) {
            return $this->updateByFarmer($request, $question);
        }

        if ($this->isExpert($request) || $this->isAdmin($request)) {
            return $this->updateByExpertOrAdmin($request, $question);
        }

        return $this->forbiddenResponse('Bạn không có quyền cập nhật câu hỏi kỹ thuật.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $question = Question::find($id);

        if (!$question) {
            return $this->notFoundResponse('Không tìm thấy câu hỏi kỹ thuật.');
        }

        if ($this->isFarmer($request)) {
            if ($question->user_id !== $request->user()->id) {
                return $this->notFoundResponse('Không tìm thấy câu hỏi kỹ thuật hoặc bạn không có quyền xóa.');
            }

            if ($question->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể xóa câu hỏi khi đang chờ trả lời.',
                ], 409);
            }

            $question->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa câu hỏi kỹ thuật thành công.',
            ]);
        }

        if ($this->isAdmin($request)) {
            $question->delete();

            return response()->json([
                'success' => true,
                'message' => 'Quản trị viên đã xóa câu hỏi kỹ thuật thành công.',
            ]);
        }

        return $this->forbiddenResponse('Bạn không có quyền xóa câu hỏi kỹ thuật.');
    }

    private function updateByFarmer(Request $request, Question $question): JsonResponse
    {
        if ($question->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Không tìm thấy câu hỏi kỹ thuật hoặc bạn không có quyền cập nhật.');
        }

        if ($question->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể cập nhật câu hỏi khi đang chờ trả lời.',
            ], 409);
        }

        $validated = $request->validate([
            'coffee_farm_id' => ['sometimes', 'nullable', 'integer', 'exists:coffee_farms,id'],
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'content' => ['sometimes', 'required', 'string'],
            'image_url' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        if (array_key_exists('coffee_farm_id', $validated) && !empty($validated['coffee_farm_id'])) {
            $farm = $this->findOwnedFarm($request, (int) $validated['coffee_farm_id']);

            if (!$farm) {
                return $this->notFoundResponse('Không tìm thấy vườn cà phê hoặc bạn không có quyền chuyển câu hỏi sang vườn này.');
            }
        }

        $question->update($validated);

        return $this->successResponse(
            'Cập nhật câu hỏi kỹ thuật thành công.',
            $question->fresh(['user', 'coffeeFarm'])
        );
    }

    private function updateByExpertOrAdmin(Request $request, Question $question): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'answered', 'closed'])],
        ]);

        $question->update([
            'status' => $validated['status'],
        ]);

        return $this->successResponse(
            'Cập nhật trạng thái câu hỏi kỹ thuật thành công.',
            $question->fresh(['user', 'coffeeFarm'])
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