<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class CoffeeFarmApi
{
    #[OA\Get(
        path: '/api/coffee-farms',
        summary: 'Lấy danh sách vườn cà phê',
        description: 'Farmer lấy danh sách vườn cà phê của chính mình. Admin/Expert có thể xem theo nghiệp vụ hệ thống.',
        security: [['bearerAuth' => []]],
        tags: ['Coffee Farms'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy danh sách vườn cà phê thành công'
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
        path: '/api/coffee-farms',
        summary: 'Tạo vườn cà phê',
        description: 'Farmer tạo mới một vườn cà phê của mình.',
        security: [['bearerAuth' => []]],
        tags: ['Coffee Farms'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['farm_name'],
                properties: [
                    new OA\Property(property: 'farm_name', type: 'string', example: 'Vườn cà phê Ea Tul'),
                    new OA\Property(property: 'province', type: 'string', example: 'Đắk Lắk'),
                    new OA\Property(property: 'district', type: 'string', example: 'Cư Mgar'),
                    new OA\Property(property: 'address', type: 'string', example: 'Xã Ea Tul, huyện Cư Mgar'),
                    new OA\Property(property: 'area', type: 'number', format: 'float', example: 2.5),
                    new OA\Property(property: 'coffee_variety', type: 'string', example: 'Robusta'),
                    new OA\Property(property: 'planting_year', type: 'integer', example: 2020),
                    new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 12.6667),
                    new OA\Property(property: 'longitude', type: 'number', format: 'float', example: 108.05),
                    new OA\Property(property: 'description', type: 'string', example: 'Vườn cà phê trồng giống Robusta.'),
                    new OA\Property(property: 'status', type: 'string', example: 'active'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Tạo vườn cà phê thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Không có quyền tạo vườn cà phê'
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
        path: '/api/coffee-farms/{id}',
        summary: 'Lấy chi tiết vườn cà phê',
        description: 'Lấy thông tin chi tiết một vườn cà phê theo ID.',
        security: [['bearerAuth' => []]],
        tags: ['Coffee Farms'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID vườn cà phê',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy chi tiết vườn cà phê thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy vườn cà phê'
            ),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: '/api/coffee-farms/{id}',
        summary: 'Cập nhật vườn cà phê',
        description: 'Farmer cập nhật thông tin vườn cà phê của chính mình.',
        security: [['bearerAuth' => []]],
        tags: ['Coffee Farms'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID vườn cà phê',
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
                    new OA\Property(property: 'farm_name', type: 'string', example: 'Vườn cà phê Ea Tul đã cập nhật'),
                    new OA\Property(property: 'province', type: 'string', example: 'Đắk Lắk'),
                    new OA\Property(property: 'district', type: 'string', example: 'Cư Mgar'),
                    new OA\Property(property: 'address', type: 'string', example: 'Xã Ea Tul, huyện Cư Mgar'),
                    new OA\Property(property: 'area', type: 'number', format: 'float', example: 3.0),
                    new OA\Property(property: 'coffee_variety', type: 'string', example: 'Robusta'),
                    new OA\Property(property: 'planting_year', type: 'integer', example: 2020),
                    new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 12.6667),
                    new OA\Property(property: 'longitude', type: 'number', format: 'float', example: 108.05),
                    new OA\Property(property: 'description', type: 'string', example: 'Cập nhật thông tin vườn.'),
                    new OA\Property(property: 'status', type: 'string', example: 'active'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cập nhật vườn cà phê thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy vườn cà phê'
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
        path: '/api/coffee-farms/{id}',
        summary: 'Xóa vườn cà phê',
        description: 'Farmer xóa vườn cà phê của chính mình nếu thỏa điều kiện nghiệp vụ.',
        security: [['bearerAuth' => []]],
        tags: ['Coffee Farms'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID vườn cà phê',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Xóa vườn cà phê thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy vườn cà phê'
            ),
        ]
    )]
    public function destroy(): void
    {
    }
}