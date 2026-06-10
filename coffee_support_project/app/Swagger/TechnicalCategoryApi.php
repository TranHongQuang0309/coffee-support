<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class TechnicalCategoryApi
{
    #[OA\Get(
        path: '/api/technical-categories',
        summary: 'Lấy danh sách danh mục kỹ thuật',
        description: 'Lấy danh sách danh mục bài viết kỹ thuật. Farmer/Expert/Admin đều có thể xem.',
        security: [['bearerAuth' => []]],
        tags: ['Technical Categories'],
        parameters: [
            new OA\Parameter(
                name: 'status',
                description: 'Lọc theo trạng thái danh mục',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'active'
            ),
            new OA\Parameter(
                name: 'keyword',
                description: 'Từ khóa tìm kiếm theo tên danh mục',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'bón phân'
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lấy danh sách danh mục kỹ thuật thành công'),
            new OA\Response(response: 401, description: 'Chưa đăng nhập hoặc token không hợp lệ'),
        ]
    )]
    public function index(): void
    {
    }

    #[OA\Post(
        path: '/api/technical-categories',
        summary: 'Tạo danh mục kỹ thuật',
        description: 'Admin tạo mới danh mục kỹ thuật.',
        security: [['bearerAuth' => []]],
        tags: ['Technical Categories'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Kỹ thuật bón phân'),
                    new OA\Property(property: 'slug', type: 'string', example: 'ky-thuat-bon-phan'),
                    new OA\Property(property: 'description', type: 'string', example: 'Các bài viết hướng dẫn kỹ thuật bón phân cho cây cà phê.'),
                    new OA\Property(property: 'status', type: 'string', example: 'active'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Tạo danh mục kỹ thuật thành công'),
            new OA\Response(response: 401, description: 'Chưa đăng nhập hoặc token không hợp lệ'),
            new OA\Response(response: 403, description: 'Chỉ quản trị viên mới được tạo danh mục'),
            new OA\Response(response: 422, description: 'Dữ liệu không hợp lệ'),
        ]
    )]
    public function store(): void
    {
    }

    #[OA\Get(
        path: '/api/technical-categories/{id}',
        summary: 'Lấy chi tiết danh mục kỹ thuật',
        description: 'Lấy thông tin chi tiết một danh mục kỹ thuật theo ID.',
        security: [['bearerAuth' => []]],
        tags: ['Technical Categories'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID danh mục kỹ thuật',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lấy chi tiết danh mục kỹ thuật thành công'),
            new OA\Response(response: 401, description: 'Chưa đăng nhập hoặc token không hợp lệ'),
            new OA\Response(response: 404, description: 'Không tìm thấy danh mục kỹ thuật'),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: '/api/technical-categories/{id}',
        summary: 'Cập nhật danh mục kỹ thuật',
        description: 'Admin cập nhật thông tin danh mục kỹ thuật.',
        security: [['bearerAuth' => []]],
        tags: ['Technical Categories'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID danh mục kỹ thuật',
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
                    new OA\Property(property: 'name', type: 'string', example: 'Kỹ thuật chăm sóc cà phê'),
                    new OA\Property(property: 'slug', type: 'string', example: 'ky-thuat-cham-soc-ca-phe'),
                    new OA\Property(property: 'description', type: 'string', example: 'Cập nhật mô tả danh mục.'),
                    new OA\Property(property: 'status', type: 'string', example: 'active'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Cập nhật danh mục kỹ thuật thành công'),
            new OA\Response(response: 401, description: 'Chưa đăng nhập hoặc token không hợp lệ'),
            new OA\Response(response: 403, description: 'Chỉ quản trị viên mới được cập nhật danh mục'),
            new OA\Response(response: 404, description: 'Không tìm thấy danh mục kỹ thuật'),
            new OA\Response(response: 422, description: 'Dữ liệu không hợp lệ'),
        ]
    )]
    public function update(): void
    {
    }

    #[OA\Delete(
        path: '/api/technical-categories/{id}',
        summary: 'Xóa danh mục kỹ thuật',
        description: 'Admin xóa danh mục kỹ thuật. Nếu danh mục đã có bài viết, hệ thống có thể không cho xóa.',
        security: [['bearerAuth' => []]],
        tags: ['Technical Categories'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID danh mục kỹ thuật',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Xóa danh mục kỹ thuật thành công'),
            new OA\Response(response: 401, description: 'Chưa đăng nhập hoặc token không hợp lệ'),
            new OA\Response(response: 403, description: 'Chỉ quản trị viên mới được xóa danh mục'),
            new OA\Response(response: 404, description: 'Không tìm thấy danh mục kỹ thuật'),
            new OA\Response(response: 409, description: 'Không thể xóa vì danh mục đã có bài viết'),
        ]
    )]
    public function destroy(): void
    {
    }
}