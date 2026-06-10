<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketPrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MarketPriceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'market_scope' => ['nullable', Rule::in(['domestic', 'world'])],
            'province' => ['nullable', 'string', 'max:100'],
            'coffee_type' => ['nullable', Rule::in(['robusta', 'arabica', 'mixed', 'other'])],
            'source_type' => ['nullable', Rule::in(['manual', 'api', 'sheet'])],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
        ]);

        $query = MarketPrice::with('creator')
            ->latest('price_date')
            ->latest('id');

        if (!empty($validated['market_scope'])) {
            $query->where('market_scope', $validated['market_scope']);
        }

        if (!empty($validated['province'])) {
            $query->where('province', 'like', '%' . $validated['province'] . '%');
        }

        if (!empty($validated['coffee_type'])) {
            $query->where('coffee_type', $validated['coffee_type']);
        }

        if (!empty($validated['source_type'])) {
            $query->where('source_type', $validated['source_type']);
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['from_date'])) {
            $query->whereDate('price_date', '>=', $validated['from_date']);
        }

        if (!empty($validated['to_date'])) {
            $query->whereDate('price_date', '<=', $validated['to_date']);
        }

        $marketPrices = $query->get();

        return $this->successResponse(
            'Lấy danh sách giá cà phê thành công.',
            $marketPrices
        );
    }

    public function store(Request $request): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse('Chỉ quản trị viên mới được tạo dữ liệu giá cà phê.');
        }

        $validated = $request->validate([
            'market_scope' => ['required', Rule::in(['domestic', 'world'])],
            'province' => ['nullable', 'string', 'max:100'],
            'coffee_type' => ['required', Rule::in(['robusta', 'arabica', 'mixed', 'other'])],
            'price' => ['required', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'price_date' => ['required', 'date'],
            'source' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'source_type' => ['nullable', Rule::in(['manual', 'api', 'sheet'])],
            'fetched_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        if ($validated['market_scope'] === 'domestic' && empty($validated['province'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập tỉnh/khu vực đối với giá cà phê trong nước.',
                'errors' => [
                    'province' => [
                        'Vui lòng nhập tỉnh/khu vực.',
                    ],
                ],
            ], 422);
        }

        $exists = MarketPrice::where('market_scope', $validated['market_scope'])
            ->where('province', $validated['province'] ?? null)
            ->where('coffee_type', $validated['coffee_type'])
            ->whereDate('price_date', $validated['price_date'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu giá cà phê cho khu vực, loại cà phê và ngày này đã tồn tại.',
            ], 409);
        }

        $marketPrice = MarketPrice::create([
            'created_by' => $request->user()->id,
            'market_scope' => $validated['market_scope'],
            'province' => $validated['province'] ?? null,
            'coffee_type' => $validated['coffee_type'],
            'price' => $validated['price'],
            'unit' => $validated['unit'] ?? 'VND/kg',
            'price_date' => $validated['price_date'],
            'source' => $validated['source'] ?? 'Admin nhập tay',
            'source_url' => $validated['source_url'] ?? null,
            'source_type' => $validated['source_type'] ?? 'manual',
            'fetched_at' => $validated['fetched_at'] ?? null,
            'note' => $validated['note'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        $marketPrice->load('creator');

        return $this->successResponse(
            'Tạo dữ liệu giá cà phê thành công.',
            $marketPrice,
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $marketPrice = MarketPrice::with('creator')->find($id);

        if (!$marketPrice) {
            return $this->notFoundResponse('Không tìm thấy dữ liệu giá cà phê.');
        }

        return $this->successResponse(
            'Lấy chi tiết giá cà phê thành công.',
            $marketPrice
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse('Chỉ quản trị viên mới được cập nhật dữ liệu giá cà phê.');
        }

        $marketPrice = MarketPrice::find($id);

        if (!$marketPrice) {
            return $this->notFoundResponse('Không tìm thấy dữ liệu giá cà phê.');
        }

        $validated = $request->validate([
            'market_scope' => ['sometimes', 'required', Rule::in(['domestic', 'world'])],
            'province' => ['sometimes', 'nullable', 'string', 'max:100'],
            'coffee_type' => ['sometimes', 'required', Rule::in(['robusta', 'arabica', 'mixed', 'other'])],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'unit' => ['sometimes', 'nullable', 'string', 'max:50'],
            'price_date' => ['sometimes', 'required', 'date'],
            'source' => ['sometimes', 'nullable', 'string', 'max:255'],
            'source_url' => ['sometimes', 'nullable', 'url', 'max:500'],
            'source_type' => ['sometimes', 'required', Rule::in(['manual', 'api', 'sheet'])],
            'fetched_at' => ['sometimes', 'nullable', 'date'],
            'note' => ['sometimes', 'nullable', 'string'],
            'status' => ['sometimes', 'required', Rule::in(['active', 'inactive'])],
        ]);

        $newMarketScope = $validated['market_scope'] ?? $marketPrice->market_scope;
        $newProvince = array_key_exists('province', $validated)
            ? $validated['province']
            : $marketPrice->province;

        if ($newMarketScope === 'domestic' && empty($newProvince)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập tỉnh/khu vực đối với giá cà phê trong nước.',
                'errors' => [
                    'province' => [
                        'Vui lòng nhập tỉnh/khu vực.',
                    ],
                ],
            ], 422);
        }

        $newCoffeeType = $validated['coffee_type'] ?? $marketPrice->coffee_type;
        $newPriceDate = $validated['price_date'] ?? $marketPrice->price_date;

        $exists = MarketPrice::where('market_scope', $newMarketScope)
            ->where('province', $newProvince)
            ->where('coffee_type', $newCoffeeType)
            ->whereDate('price_date', $newPriceDate)
            ->where('id', '!=', $marketPrice->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu giá cà phê cho khu vực, loại cà phê và ngày này đã tồn tại.',
            ], 409);
        }

        $marketPrice->update($validated);

        return $this->successResponse(
            'Cập nhật dữ liệu giá cà phê thành công.',
            $marketPrice->fresh('creator')
        );
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse('Chỉ quản trị viên mới được xóa dữ liệu giá cà phê.');
        }

        $marketPrice = MarketPrice::find($id);

        if (!$marketPrice) {
            return $this->notFoundResponse('Không tìm thấy dữ liệu giá cà phê.');
        }

        $marketPrice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa dữ liệu giá cà phê thành công.',
        ]);
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