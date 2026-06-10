<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class AnswerApi
{
    #[OA\Get(
        path: '/api/questions/{question}/answers',
        summary: 'Lấy danh sách câu trả lời của câu hỏi',
        description: 'Lấy danh sách câu trả lời thuộc một câu hỏi kỹ thuật. Farmer xem câu trả lời của câu hỏi mình được phép xem. Expert/Admin xem theo quyền hệ thống.',
        security: [['bearerAuth' => []]],
        tags: ['Answers'],
        parameters: [
            new OA\Parameter(
                name: 'question',
                description: 'ID câu hỏi kỹ thuật',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
            new OA\Parameter(
                name: 'status',
                description: 'Lọc theo trạng thái câu trả lời',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['visible', 'hidden']
                ),
                example: 'visible'
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy danh sách câu trả lời thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Không có quyền xem câu trả lời'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy câu hỏi kỹ thuật'
            ),
        ]
    )]
    public function index(): void
    {
    }

    #[OA\Post(
        path: '/api/questions/{question}/answers',
        summary: 'Tạo câu trả lời cho câu hỏi',
        description: 'Expert/Admin tạo câu trả lời cho một câu hỏi kỹ thuật. Khi có câu trả lời, câu hỏi có thể được chuyển sang trạng thái answered.',
        security: [['bearerAuth' => []]],
        tags: ['Answers'],
        parameters: [
            new OA\Parameter(
                name: 'question',
                description: 'ID câu hỏi kỹ thuật cần trả lời',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['content'],
                properties: [
                    new OA\Property(
                        property: 'content',
                        type: 'string',
                        example: 'Dựa trên mô tả, cây cà phê có thể đang bị ảnh hưởng bởi nấm sau mưa kéo dài. Anh/chị nên tỉa cành tạo thông thoáng, kiểm tra mặt dưới lá và áp dụng biện pháp phòng trừ phù hợp.'
                    ),
                    new OA\Property(
                        property: 'image_url',
                        type: 'string',
                        nullable: true,
                        example: 'answers/huong-dan-xu-ly-vang-la.jpg'
                    ),
                    new OA\Property(
                        property: 'status',
                        type: 'string',
                        enum: ['visible', 'hidden'],
                        example: 'visible'
                    ),
                    new OA\Property(
                        property: 'is_accepted',
                        type: 'boolean',
                        example: false
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Tạo câu trả lời thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Chỉ chuyên gia hoặc quản trị viên mới được trả lời'
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
    public function store(): void
    {
    }

    #[OA\Get(
        path: '/api/answers/{answer}',
        summary: 'Lấy chi tiết câu trả lời',
        description: 'Lấy chi tiết một câu trả lời theo ID.',
        security: [['bearerAuth' => []]],
        tags: ['Answers'],
        parameters: [
            new OA\Parameter(
                name: 'answer',
                description: 'ID câu trả lời',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy chi tiết câu trả lời thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Không có quyền xem câu trả lời'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy câu trả lời'
            ),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: '/api/answers/{answer}',
        summary: 'Cập nhật câu trả lời',
        description: 'Expert có thể cập nhật câu trả lời của mình. Admin có thể cập nhật tất cả câu trả lời, bao gồm trạng thái và đánh dấu câu trả lời được chấp nhận.',
        security: [['bearerAuth' => []]],
        tags: ['Answers'],
        parameters: [
            new OA\Parameter(
                name: 'answer',
                description: 'ID câu trả lời',
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
                        property: 'content',
                        type: 'string',
                        example: 'Cập nhật nội dung tư vấn: cần kiểm tra thêm độ ẩm đất, tình trạng rễ và mức độ lan rộng của triệu chứng trước khi xử lý.'
                    ),
                    new OA\Property(
                        property: 'image_url',
                        type: 'string',
                        nullable: true,
                        example: 'answers/cap-nhat-huong-dan.jpg'
                    ),
                    new OA\Property(
                        property: 'status',
                        type: 'string',
                        enum: ['visible', 'hidden'],
                        example: 'visible'
                    ),
                    new OA\Property(
                        property: 'is_accepted',
                        type: 'boolean',
                        example: true
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cập nhật câu trả lời thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Không có quyền cập nhật câu trả lời'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy câu trả lời'
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

    #[OA\Patch(
        path: '/api/answers/{answer}',
        summary: 'Cập nhật một phần câu trả lời',
        description: 'Cập nhật một phần thông tin câu trả lời, ví dụ chỉ cập nhật trạng thái hoặc đánh dấu câu trả lời được chấp nhận.',
        security: [['bearerAuth' => []]],
        tags: ['Answers'],
        parameters: [
            new OA\Parameter(
                name: 'answer',
                description: 'ID câu trả lời',
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
                        property: 'content',
                        type: 'string',
                        example: 'Nội dung cập nhật ngắn.'
                    ),
                    new OA\Property(
                        property: 'status',
                        type: 'string',
                        enum: ['visible', 'hidden'],
                        example: 'hidden'
                    ),
                    new OA\Property(
                        property: 'is_accepted',
                        type: 'boolean',
                        example: false
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cập nhật một phần câu trả lời thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Không có quyền cập nhật câu trả lời'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy câu trả lời'
            ),
            new OA\Response(
                response: 422,
                description: 'Dữ liệu không hợp lệ'
            ),
        ]
    )]
    public function partialUpdate(): void
    {
    }

    #[OA\Delete(
        path: '/api/answers/{answer}',
        summary: 'Xóa câu trả lời',
        description: 'Expert có thể xóa câu trả lời của mình. Admin có thể xóa tất cả câu trả lời. Nếu xóa câu trả lời cuối cùng, câu hỏi có thể quay về trạng thái pending.',
        security: [['bearerAuth' => []]],
        tags: ['Answers'],
        parameters: [
            new OA\Parameter(
                name: 'answer',
                description: 'ID câu trả lời',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Xóa câu trả lời thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Không có quyền xóa câu trả lời'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy câu trả lời'
            ),
        ]
    )]
    public function destroy(): void
    {
    }
}