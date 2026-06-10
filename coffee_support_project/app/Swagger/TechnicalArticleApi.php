<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class TechnicalArticleApi
{
    #[OA\Get(
        path: '/api/technical-articles',
        summary: 'Lấy danh sách bài viết kỹ thuật',
        description: 'Lấy danh sách bài viết kỹ thuật. Có thể lọc theo danh mục, trạng thái, nguồn, xác thực và từ khóa.',
        security: [['bearerAuth' => []]],
        tags: ['Technical Articles'],
        parameters: [
            new OA\Parameter(
                name: 'technical_category_id',
                description: 'ID danh mục kỹ thuật',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
            new OA\Parameter(
                name: 'status',
                description: 'Trạng thái bài viết',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['draft', 'published', 'hidden']),
                example: 'published'
            ),
            new OA\Parameter(
                name: 'keyword',
                description: 'Từ khóa tìm kiếm theo tiêu đề, tóm tắt hoặc nội dung',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'bón phân'
            ),
            new OA\Parameter(
                name: 'source_type',
                description: 'Loại nguồn bài viết',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['self_written', 'external', 'summarized', 'expert_contributed']),
                example: 'external'
            ),
            new OA\Parameter(
                name: 'is_verified',
                description: 'Trạng thái xác thực nội dung',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean'),
                example: true
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lấy danh sách bài viết kỹ thuật thành công'),
            new OA\Response(response: 401, description: 'Chưa đăng nhập hoặc token không hợp lệ'),
            new OA\Response(response: 422, description: 'Dữ liệu lọc không hợp lệ'),
        ]
    )]
    public function index(): void
    {
    }

    #[OA\Post(
        path: '/api/technical-articles',
        summary: 'Tạo bài viết kỹ thuật',
        description: 'Admin tạo bài viết kỹ thuật. Bài viết có thể là bản nháp, đã xuất bản hoặc ẩn.',
        security: [['bearerAuth' => []]],
        tags: ['Technical Articles'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['technical_category_id', 'title', 'content'],
                properties: [
                    new OA\Property(property: 'technical_category_id', type: 'integer', example: 1),
                    new OA\Property(property: 'title', type: 'string', example: 'Kỹ thuật bón phân cho cà phê giai đoạn kinh doanh'),
                    new OA\Property(property: 'slug', type: 'string', example: 'ky-thuat-bon-phan-cho-ca-phe-giai-doan-kinh-doanh'),
                    new OA\Property(property: 'summary', type: 'string', example: 'Hướng dẫn bón phân cân đối cho cây cà phê nhằm tăng năng suất và hạn chế sâu bệnh.'),
                    new OA\Property(property: 'content', type: 'string', example: 'Nội dung chi tiết bài viết kỹ thuật...'),
                    new OA\Property(property: 'thumbnail_url', type: 'string', example: 'articles/bon-phan-ca-phe.jpg'),
                    new OA\Property(property: 'status', type: 'string', enum: ['draft', 'published', 'hidden'], example: 'published'),
                    new OA\Property(property: 'published_at', type: 'string', format: 'date-time', example: '2026-06-10 09:00:00'),
                    new OA\Property(property: 'source_type', type: 'string', enum: ['self_written', 'external', 'summarized', 'expert_contributed'], example: 'external'),
                    new OA\Property(property: 'source_name', type: 'string', example: 'Tài liệu kỹ thuật nông nghiệp'),
                    new OA\Property(property: 'source_url', type: 'string', example: 'https://example.com/tai-lieu-ca-phe'),
                    new OA\Property(property: 'is_verified', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Tạo bài viết kỹ thuật thành công'),
            new OA\Response(response: 401, description: 'Chưa đăng nhập hoặc token không hợp lệ'),
            new OA\Response(response: 403, description: 'Chỉ quản trị viên mới được tạo bài viết'),
            new OA\Response(response: 422, description: 'Dữ liệu không hợp lệ'),
        ]
    )]
    public function store(): void
    {
    }

    #[OA\Get(
        path: '/api/technical-articles/{id}',
        summary: 'Lấy chi tiết bài viết kỹ thuật',
        description: 'Lấy chi tiết bài viết kỹ thuật theo ID. Hệ thống có thể tăng view_count khi xem chi tiết.',
        security: [['bearerAuth' => []]],
        tags: ['Technical Articles'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID bài viết kỹ thuật',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lấy chi tiết bài viết kỹ thuật thành công'),
            new OA\Response(response: 401, description: 'Chưa đăng nhập hoặc token không hợp lệ'),
            new OA\Response(response: 404, description: 'Không tìm thấy bài viết kỹ thuật'),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: '/api/technical-articles/{id}',
        summary: 'Cập nhật bài viết kỹ thuật',
        description: 'Admin cập nhật bài viết kỹ thuật, trạng thái xuất bản và thông tin xác thực nguồn.',
        security: [['bearerAuth' => []]],
        tags: ['Technical Articles'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID bài viết kỹ thuật',
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
                    new OA\Property(property: 'technical_category_id', type: 'integer', example: 1),
                    new OA\Property(property: 'title', type: 'string', example: 'Cập nhật kỹ thuật bón phân cho cà phê'),
                    new OA\Property(property: 'slug', type: 'string', example: 'cap-nhat-ky-thuat-bon-phan-cho-ca-phe'),
                    new OA\Property(property: 'summary', type: 'string', example: 'Tóm tắt bài viết đã cập nhật.'),
                    new OA\Property(property: 'content', type: 'string', example: 'Nội dung cập nhật...'),
                    new OA\Property(property: 'thumbnail_url', type: 'string', example: 'articles/cap-nhat-bon-phan.jpg'),
                    new OA\Property(property: 'status', type: 'string', enum: ['draft', 'published', 'hidden'], example: 'published'),
                    new OA\Property(property: 'published_at', type: 'string', format: 'date-time', example: '2026-06-10 10:00:00'),
                    new OA\Property(property: 'source_type', type: 'string', enum: ['self_written', 'external', 'summarized', 'expert_contributed'], example: 'summarized'),
                    new OA\Property(property: 'source_name', type: 'string', example: 'Nguồn tổng hợp kỹ thuật cà phê'),
                    new OA\Property(property: 'source_url', type: 'string', example: 'https://example.com/source'),
                    new OA\Property(property: 'is_verified', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Cập nhật bài viết kỹ thuật thành công'),
            new OA\Response(response: 401, description: 'Chưa đăng nhập hoặc token không hợp lệ'),
            new OA\Response(response: 403, description: 'Chỉ quản trị viên mới được cập nhật bài viết'),
            new OA\Response(response: 404, description: 'Không tìm thấy bài viết kỹ thuật'),
            new OA\Response(response: 422, description: 'Dữ liệu không hợp lệ'),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/technical-articles/{id}',
        summary: 'Xóa bài viết kỹ thuật',
        description: 'Admin xóa bài viết kỹ thuật khỏi hệ thống.',
        security: [['bearerAuth' => []]],
        tags: ['Technical Articles'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID bài viết kỹ thuật',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Xóa bài viết kỹ thuật thành công'),
            new OA\Response(response: 401, description: 'Chưa đăng nhập hoặc token không hợp lệ'),
            new OA\Response(response: 403, description: 'Chỉ quản trị viên mới được xóa bài viết'),
            new OA\Response(response: 404, description: 'Không tìm thấy bài viết kỹ thuật'),
        ]
    )]
    public function destroy(): void
    {
    }
}