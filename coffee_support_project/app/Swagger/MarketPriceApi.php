<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class MarketPriceApi
{
    #[OA\Get(
        path: '/api/market-prices',
        summary: 'Lấy danh sách giá cà phê thị trường',
        description: 'Lấy danh sách giá cà phê thị trường. Có thể lọc theo phạm vi thị trường, tỉnh, loại cà phê, nguồn dữ liệu, trạng thái và khoảng ngày.',
        security: [['bearerAuth' => []]],
        tags: ['Market Prices'],
        parameters: [
            new OA\Parameter(
                name: 'market_scope',
                description: 'Phạm vi thị trường',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['domestic', 'world']
                ),
                example: 'domestic'
            ),
            new OA\Parameter(
                name: 'province',
                description: 'Tỉnh/khu vực giá cà phê',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'Đắk Lắk'
            ),
            new OA\Parameter(
                name: 'coffee_type',
                description: 'Loại cà phê',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['robusta', 'arabica', 'mixed', 'other']
                ),
                example: 'robusta'
            ),
            new OA\Parameter(
                name: 'source_type',
                description: 'Loại nguồn dữ liệu',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['manual', 'api', 'sheet']
                ),
                example: 'manual'
            ),
            new OA\Parameter(
                name: 'status',
                description: 'Trạng thái bản ghi giá',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['active', 'inactive']
                ),
                example: 'active'
            ),
            new OA\Parameter(
                name: 'from_date',
                description: 'Ngày bắt đầu lọc giá',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date'),
                example: '2026-06-01'
            ),
            new OA\Parameter(
                name: 'to_date',
                description: 'Ngày kết thúc lọc giá',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date'),
                example: '2026-06-10'
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy danh sách giá cà phê thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 422,
                description: 'Dữ liệu lọc không hợp lệ'
            ),
        ]
    )]
    public function index(): void
    {
    }

    #[OA\Post(
        path: '/api/market-prices',
        summary: 'Tạo giá cà phê thị trường',
        description: 'Admin tạo mới bản ghi giá cà phê thị trường. Giai đoạn hiện tại ưu tiên nhập tay dữ liệu giá.',
        security: [['bearerAuth' => []]],
        tags: ['Market Prices'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['market_scope', 'coffee_type', 'price', 'unit', 'price_date'],
                properties: [
                    new OA\Property(
                        property: 'market_scope',
                        type: 'string',
                        enum: ['domestic', 'world'],
                        example: 'domestic'
                    ),
                    new OA\Property(
                        property: 'province',
                        type: 'string',
                        nullable: true,
                        example: 'Đắk Lắk',
                        description: 'Bắt buộc khi market_scope là domestic'
                    ),
                    new OA\Property(
                        property: 'coffee_type',
                        type: 'string',
                        enum: ['robusta', 'arabica', 'mixed', 'other'],
                        example: 'robusta'
                    ),
                    new OA\Property(
                        property: 'price',
                        type: 'number',
                        format: 'float',
                        example: 125000
                    ),
                    new OA\Property(
                        property: 'unit',
                        type: 'string',
                        example: 'VND/kg'
                    ),
                    new OA\Property(
                        property: 'price_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-06-10'
                    ),
                    new OA\Property(
                        property: 'source',
                        type: 'string',
                        nullable: true,
                        example: 'Admin nhập tay'
                    ),
                    new OA\Property(
                        property: 'source_url',
                        type: 'string',
                        nullable: true,
                        example: 'https://example.com/gia-ca-phe'
                    ),
                    new OA\Property(
                        property: 'source_type',
                        type: 'string',
                        enum: ['manual', 'api', 'sheet'],
                        example: 'manual'
                    ),
                    new OA\Property(
                        property: 'fetched_at',
                        type: 'string',
                        format: 'date-time',
                        nullable: true,
                        example: '2026-06-10 08:00:00'
                    ),
                    new OA\Property(
                        property: 'note',
                        type: 'string',
                        nullable: true,
                        example: 'Giá tham khảo cho khu vực Tây Nguyên.'
                    ),
                    new OA\Property(
                        property: 'status',
                        type: 'string',
                        enum: ['active', 'inactive'],
                        example: 'active'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Tạo giá cà phê thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Chỉ quản trị viên mới được tạo giá cà phê'
            ),
            new OA\Response(
                response: 409,
                description: 'Giá cà phê cho ngày/khu vực/loại cà phê này đã tồn tại'
            ),
            new OA\Response(
                response: 422,
                description: 'Dữ liệu không hợp lệ'
            ),
        ]
    )]
    public function store(): void
    {
    }

    #[OA\Get(
        path: '/api/market-prices/{id}',
        summary: 'Lấy chi tiết giá cà phê',
        description: 'Lấy chi tiết một bản ghi giá cà phê thị trường theo ID.',
        security: [['bearerAuth' => []]],
        tags: ['Market Prices'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID bản ghi giá cà phê',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy chi tiết giá cà phê thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy giá cà phê'
            ),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: '/api/market-prices/{id}',
        summary: 'Cập nhật giá cà phê thị trường',
        description: 'Admin cập nhật thông tin giá cà phê thị trường.',
        security: [['bearerAuth' => []]],
        tags: ['Market Prices'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID bản ghi giá cà phê',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'market_scope',
                        type: 'string',
                        enum: ['domestic', 'world'],
                        example: 'domestic'
                    ),
                    new OA\Property(
                        property: 'province',
                        type: 'string',
                        nullable: true,
                        example: 'Gia Lai'
                    ),
                    new OA\Property(
                        property: 'coffee_type',
                        type: 'string',
                        enum: ['robusta', 'arabica', 'mixed', 'other'],
                        example: 'robusta'
                    ),
                    new OA\Property(
                        property: 'price',
                        type: 'number',
                        format: 'float',
                        example: 126500
                    ),
                    new OA\Property(
                        property: 'unit',
                        type: 'string',
                        example: 'VND/kg'
                    ),
                    new OA\Property(
                        property: 'price_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-06-10'
                    ),
                    new OA\Property(
                        property: 'source',
                        type: 'string',
                        nullable: true,
                        example: 'Admin cập nhật'
                    ),
                    new OA\Property(
                        property: 'source_url',
                        type: 'string',
                        nullable: true,
                        example: 'https://example.com/gia-ca-phe-cap-nhat'
                    ),
                    new OA\Property(
                        property: 'source_type',
                        type: 'string',
                        enum: ['manual', 'api', 'sheet'],
                        example: 'manual'
                    ),
                    new OA\Property(
                        property: 'fetched_at',
                        type: 'string',
                        format: 'date-time',
                        nullable: true,
                        example: '2026-06-10 09:00:00'
                    ),
                    new OA\Property(
                        property: 'note',
                        type: 'string',
                        nullable: true,
                        example: 'Cập nhật giá theo thị trường trong ngày.'
                    ),
                    new OA\Property(
                        property: 'status',
                        type: 'string',
                        enum: ['active', 'inactive'],
                        example: 'active'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cập nhật giá cà phê thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Chỉ quản trị viên mới được cập nhật giá cà phê'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy giá cà phê'
            ),
            new OA\Response(
                response: 409,
                description: 'Giá cà phê cho ngày/khu vực/loại cà phê này đã tồn tại'
            ),
            new OA\Response(
                response: 422,
                description: 'Dữ liệu không hợp lệ'
            ),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/market-prices/{id}',
        summary: 'Xóa giá cà phê thị trường',
        description: 'Admin xóa một bản ghi giá cà phê thị trường khỏi hệ thống.',
        security: [['bearerAuth' => []]],
        tags: ['Market Prices'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID bản ghi giá cà phê',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Xóa giá cà phê thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Chỉ quản trị viên mới được xóa giá cà phê'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy giá cà phê'
            ),
        ]
    )]
    public function destroy(): void
    {
    }
}