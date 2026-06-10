<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnswerController extends Controller
{
    public function index(Request $request, int $questionId): JsonResponse
    {
        $question = Question::find($questionId);

        if (!$question) {
            return $this->notFoundResponse('Không tìm thấy câu hỏi kỹ thuật.');
        }

        if ($this->isFarmer($request) && $question->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Không tìm thấy câu hỏi kỹ thuật hoặc bạn không có quyền xem câu trả lời.');
        }

        $query = Answer::with(['user'])
            ->where('question_id', $question->id)
            ->latest();

        if ($this->isFarmer($request)) {
            $query->where('status', 'visible');
        }

        $answers = $query->get();

        return $this->successResponse(
            'Lấy danh sách câu trả lời thành công.',
            $answers
        );
    }

    public function store(Request $request, int $questionId): JsonResponse
    {
        if (!$this->isExpert($request) && !$this->isAdmin($request)) {
            return $this->forbiddenResponse('Chỉ chuyên gia hoặc quản trị viên mới được trả lời câu hỏi.');
        }

        $question = Question::find($questionId);

        if (!$question) {
            return $this->notFoundResponse('Không tìm thấy câu hỏi kỹ thuật.');
        }

        if ($question->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Không thể trả lời câu hỏi đã đóng.',
            ], 409);
        }

        $validated = $request->validate([
            'content' => ['required', 'string'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['visible', 'hidden'])],
            'is_accepted' => ['nullable', 'boolean'],
        ]);

        $answer = Answer::create([
            'question_id' => $question->id,
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'image_url' => $validated['image_url'] ?? null,
            'status' => $validated['status'] ?? 'visible',
            'is_accepted' => $validated['is_accepted'] ?? false,
        ]);

        if ($question->status === 'pending') {
            $question->update([
                'status' => 'answered',
            ]);
        }

        if (($validated['is_accepted'] ?? false) === true) {
            $this->markAsAccepted($answer);
        }

        $answer->load(['user', 'question']);

        return $this->successResponse(
            'Tạo câu trả lời thành công.',
            $answer,
            201
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $answer = Answer::with(['user', 'question.user'])->find($id);

        if (!$answer) {
            return $this->notFoundResponse('Không tìm thấy câu trả lời.');
        }

        if ($this->isFarmer($request)) {
            $questionOwnerId = $answer->question?->user_id;

            if ($questionOwnerId !== $request->user()->id || $answer->status !== 'visible') {
                return $this->notFoundResponse('Không tìm thấy câu trả lời hoặc bạn không có quyền truy cập.');
            }
        }

        return $this->successResponse(
            'Lấy chi tiết câu trả lời thành công.',
            $answer
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $answer = Answer::with('question')->find($id);

        if (!$answer) {
            return $this->notFoundResponse('Không tìm thấy câu trả lời.');
        }

        if ($this->isExpert($request)) {
            if ($answer->user_id !== $request->user()->id) {
                return $this->forbiddenResponse('Chuyên gia chỉ được cập nhật câu trả lời của chính mình.');
            }

            return $this->updateByExpert($request, $answer);
        }

        if ($this->isAdmin($request)) {
            return $this->updateByAdmin($request, $answer);
        }

        return $this->forbiddenResponse('Bạn không có quyền cập nhật câu trả lời.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $answer = Answer::with('question')->find($id);

        if (!$answer) {
            return $this->notFoundResponse('Không tìm thấy câu trả lời.');
        }

        if ($this->isExpert($request) && $answer->user_id !== $request->user()->id) {
            return $this->forbiddenResponse('Chuyên gia chỉ được xóa câu trả lời của chính mình.');
        }

        if (!$this->isExpert($request) && !$this->isAdmin($request)) {
            return $this->forbiddenResponse('Bạn không có quyền xóa câu trả lời.');
        }

        $question = $answer->question;

        $answer->delete();

        if ($question) {
            $visibleAnswerCount = Answer::where('question_id', $question->id)
                ->where('status', 'visible')
                ->count();

            if ($visibleAnswerCount === 0 && $question->status === 'answered') {
                $question->update([
                    'status' => 'pending',
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Xóa câu trả lời thành công.',
        ]);
    }

    private function updateByExpert(Request $request, Answer $answer): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['sometimes', 'required', 'string'],
            'image_url' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $answer->update($validated);

        return $this->successResponse(
            'Cập nhật câu trả lời thành công.',
            $answer->fresh(['user', 'question'])
        );
    }

    private function updateByAdmin(Request $request, Answer $answer): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['sometimes', 'required', 'string'],
            'image_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'required', Rule::in(['visible', 'hidden'])],
            'is_accepted' => ['sometimes', 'required', 'boolean'],
        ]);

        $answer->update($validated);

        if (array_key_exists('is_accepted', $validated) && $validated['is_accepted']) {
            $this->markAsAccepted($answer);
        }

        if (array_key_exists('is_accepted', $validated) && !$validated['is_accepted']) {
            $answer->update([
                'is_accepted' => false,
            ]);
        }

        return $this->successResponse(
            'Quản trị viên đã cập nhật câu trả lời thành công.',
            $answer->fresh(['user', 'question'])
        );
    }

    private function markAsAccepted(Answer $answer): void
    {
        Answer::where('question_id', $answer->question_id)
            ->where('id', '!=', $answer->id)
            ->update([
                'is_accepted' => false,
            ]);

        $answer->update([
            'is_accepted' => true,
        ]);
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