<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class CultivationLogApi
{
    #[OA\Get(
        path: '/api/cultivation-logs',
        summary: 'Lấy danh sách nhật ký canh tác',
        description: 'Farmer lấy danh sách nhật ký canh tác của chính mình. Có thể lọc theo vườn, loại hoạt động, trạng thái và khoảng ngày.',
        security: [['bearerAuth' => []]],
        tags: ['Cultivation Logs'],
        parameters: [
            new OA\Parameter(
                name: 'coffee_farm_id',
                description: 'ID vườn cà phê cần lọc',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
            new OA\Parameter(
                name: 'activity_type',
                description: 'Loại hoạt động canh tác',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'fertilizing'
            ),
            new OA\Parameter(
                name: 'status',
                description: 'Trạng thái nhật ký',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'completed'
            ),
            new OA\Parameter(
                name: 'from_date',
                description: 'Ngày bắt đầu lọc',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date'),
                example: '2026-06-01'
            ),
            new OA\Parameter(
                name: 'to_date',
                description: 'Ngày kết thúc lọc',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date'),
                example: '2026-06-10'
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy danh sách nhật ký canh tác thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
        ]
    )]
    public function index(): void
    {
    }

    #[OA\Post(
        path: '/api/cultivation-logs',
        summary: 'Tạo nhật ký canh tác',
        description: 'Farmer tạo nhật ký canh tác cho một vườn cà phê thuộc quyền sở hữu của mình.',
        security: [['bearerAuth' => []]],
        tags: ['Cultivation Logs'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['coffee_farm_id', 'activity_date', 'activity_type', 'title'],
                properties: [
                    new OA\Property(
                        property: 'coffee_farm_id',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'activity_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-06-10'
                    ),
                    new OA\Property(
                        property: 'activity_type',
                        type: 'string',
                        example: 'fertilizing',
                        description: 'Ví dụ: watering, fertilizing, pruning, spraying, harvesting, other'
                    ),
                    new OA\Property(
                        property: 'title',
                        type: 'string',
                        example: 'Bón phân đợt 1 cho vườn cà phê'
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        example: 'Bón phân NPK cho toàn bộ khu vực vườn sau mưa.'
                    ),
                    new OA\Property(
                        property: 'materials_used',
                        type: 'string',
                        example: 'NPK 16-16-8, phân hữu cơ'
                    ),
                    new OA\Property(
                        property: 'cost',
                        type: 'number',
                        format: 'float',
                        example: 1500000
                    ),
                    new OA\Property(
                        property: 'labor_count',
                        type: 'integer',
                        example: 2
                    ),
                    new OA\Property(
                        property: 'image_url',
                        type: 'string',
                        example: 'cultivation-logs/bon-phan-dot-1.jpg'
                    ),
                    new OA\Property(
                        property: 'status',
                        type: 'string',
                        example: 'completed'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Tạo nhật ký canh tác thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Không có quyền tạo nhật ký canh tác'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy vườn cà phê hoặc không có quyền truy cập'
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
        path: '/api/cultivation-logs/{id}',
        summary: 'Lấy chi tiết nhật ký canh tác',
        description: 'Lấy chi tiết một bản ghi nhật ký canh tác theo ID.',
        security: [['bearerAuth' => []]],
        tags: ['Cultivation Logs'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID nhật ký canh tác',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy chi tiết nhật ký canh tác thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy nhật ký canh tác'
            ),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: '/api/cultivation-logs/{id}',
        summary: 'Cập nhật nhật ký canh tác',
        description: 'Farmer cập nhật nhật ký canh tác thuộc vườn của chính mình.',
        security: [['bearerAuth' => []]],
        tags: ['Cultivation Logs'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID nhật ký canh tác',
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
                        property: 'coffee_farm_id',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'activity_date',
                        type: 'string',
                        format: 'date',
                        example: '2026-06-11'
                    ),
                    new OA\Property(
                        property: 'activity_type',
                        type: 'string',
                        example: 'spraying'
                    ),
                    new OA\Property(
                        property: 'title',
                        type: 'string',
                        example: 'Phun phòng bệnh gỉ sắt'
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        example: 'Phun thuốc phòng bệnh sau giai đoạn mưa kéo dài.'
                    ),
                    new OA\Property(
                        property: 'materials_used',
                        type: 'string',
                        example: 'Thuốc phòng nấm, bình phun'
                    ),
                    new OA\Property(
                        property: 'cost',
                        type: 'number',
                        format: 'float',
                        example: 800000
                    ),
                    new OA\Property(
                        property: 'labor_count',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'image_url',
                        type: 'string',
                        example: 'cultivation-logs/phun-phong-benh.jpg'
                    ),
                    new OA\Property(
                        property: 'status',
                        type: 'string',
                        example: 'completed'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cập nhật nhật ký canh tác thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy nhật ký canh tác'
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
        path: '/api/cultivation-logs/{id}',
        summary: 'Xóa nhật ký canh tác',
        description: 'Farmer xóa nhật ký canh tác thuộc quyền sở hữu của mình.',
        security: [['bearerAuth' => []]],
        tags: ['Cultivation Logs'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID nhật ký canh tác',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Xóa nhật ký canh tác thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy nhật ký canh tác'
            ),
        ]
    )]
    public function destroy(): void
    {
    }
}