<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class QuestionApi
{
    #[OA\Get(
        path: '/api/questions',
        summary: 'Lấy danh sách câu hỏi kỹ thuật',
        description: 'Farmer xem danh sách câu hỏi của mình. Expert/Admin có thể xem danh sách câu hỏi để trả lời hoặc quản lý.',
        security: [['bearerAuth' => []]],
        tags: ['Questions'],
        parameters: [
            new OA\Parameter(
                name: 'status',
                description: 'Lọc theo trạng thái câu hỏi',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['pending', 'answered', 'closed']
                ),
                example: 'pending'
            ),
            new OA\Parameter(
                name: 'coffee_farm_id',
                description: 'ID vườn cà phê liên quan đến câu hỏi',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
            new OA\Parameter(
                name: 'keyword',
                description: 'Từ khóa tìm kiếm theo tiêu đề hoặc nội dung câu hỏi',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'lá cà phê bị vàng'
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
                description: 'Lấy danh sách câu hỏi kỹ thuật thành công'
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
        path: '/api/questions',
        summary: 'Tạo câu hỏi kỹ thuật',
        description: 'Farmer tạo câu hỏi kỹ thuật để được chuyên gia hoặc quản trị viên hỗ trợ.',
        security: [['bearerAuth' => []]],
        tags: ['Questions'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'content'],
                properties: [
                    new OA\Property(
                        property: 'coffee_farm_id',
                        type: 'integer',
                        nullable: true,
                        example: 1
                    ),
                    new OA\Property(
                        property: 'title',
                        type: 'string',
                        example: 'Lá cà phê bị vàng sau nhiều ngày mưa'
                    ),
                    new OA\Property(
                        property: 'content',
                        type: 'string',
                        example: 'Vườn cà phê của tôi bị vàng lá, một số cây có hiện tượng rụng lá sau nhiều ngày mưa. Tôi nên xử lý như thế nào?'
                    ),
                    new OA\Property(
                        property: 'image_url',
                        type: 'string',
                        nullable: true,
                        example: 'questions/la-ca-phe-bi-vang.jpg'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Tạo câu hỏi kỹ thuật thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Không có quyền tạo câu hỏi kỹ thuật'
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
        path: '/api/questions/{id}',
        summary: 'Lấy chi tiết câu hỏi kỹ thuật',
        description: 'Lấy chi tiết một câu hỏi kỹ thuật theo ID, có thể bao gồm thông tin người hỏi, vườn cà phê và danh sách câu trả lời.',
        security: [['bearerAuth' => []]],
        tags: ['Questions'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID câu hỏi kỹ thuật',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy chi tiết câu hỏi kỹ thuật thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy câu hỏi kỹ thuật'
            ),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: '/api/questions/{id}',
        summary: 'Cập nhật câu hỏi kỹ thuật',
        description: 'Farmer có thể cập nhật câu hỏi của mình khi câu hỏi còn ở trạng thái pending. Expert/Admin có thể cập nhật trạng thái theo nghiệp vụ.',
        security: [['bearerAuth' => []]],
        tags: ['Questions'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID câu hỏi kỹ thuật',
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
                        nullable: true,
                        example: 1
                    ),
                    new OA\Property(
                        property: 'title',
                        type: 'string',
                        example: 'Cập nhật câu hỏi về lá cà phê bị vàng'
                    ),
                    new OA\Property(
                        property: 'content',
                        type: 'string',
                        example: 'Tôi bổ sung thêm ảnh và mô tả hiện tượng vàng lá sau mưa.'
                    ),
                    new OA\Property(
                        property: 'image_url',
                        type: 'string',
                        nullable: true,
                        example: 'questions/cap-nhat-la-ca-phe-bi-vang.jpg'
                    ),
                    new OA\Property(
                        property: 'status',
                        type: 'string',
                        enum: ['pending', 'answered', 'closed'],
                        example: 'pending'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cập nhật câu hỏi kỹ thuật thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Không có quyền cập nhật câu hỏi kỹ thuật'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy câu hỏi kỹ thuật'
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
        path: '/api/questions/{id}',
        summary: 'Xóa câu hỏi kỹ thuật',
        description: 'Farmer có thể xóa câu hỏi của mình khi còn ở trạng thái pending. Admin có thể xóa theo quyền quản trị.',
        security: [['bearerAuth' => []]],
        tags: ['Questions'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID câu hỏi kỹ thuật',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Xóa câu hỏi kỹ thuật thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Không có quyền xóa câu hỏi kỹ thuật'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy câu hỏi kỹ thuật'
            ),
        ]
    )]
    public function destroy(): void
    {
    }
}