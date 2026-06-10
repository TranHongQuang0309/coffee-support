<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class AdminDashboardApi
{
    #[OA\Get(
        path: '/api/admin/dashboard/statistics',
        summary: 'Lấy thống kê tổng quan hệ thống',
        description: 'Admin xem thống kê tổng quan toàn hệ thống: người dùng, vườn cà phê, nhật ký canh tác, bài viết kỹ thuật, sâu bệnh, chẩn đoán, hỏi đáp, giá thị trường và dữ liệu thời tiết cache.',
        security: [['bearerAuth' => []]],
        tags: ['Admin Dashboard'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy thống kê hệ thống thành công',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'success',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Lấy thống kê hệ thống thành công.'
                        ),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'users',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'total', type: 'integer', example: 20),
                                        new OA\Property(property: 'farmers', type: 'integer', example: 15),
                                        new OA\Property(property: 'experts', type: 'integer', example: 3),
                                        new OA\Property(property: 'admins', type: 'integer', example: 2),
                                        new OA\Property(property: 'active', type: 'integer', example: 18),
                                        new OA\Property(property: 'locked', type: 'integer', example: 1),
                                        new OA\Property(property: 'inactive', type: 'integer', example: 1),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'farms',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'total', type: 'integer', example: 12),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'cultivation',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'logs', type: 'integer', example: 35),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'content',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'technical_categories', type: 'integer', example: 6),
                                        new OA\Property(
                                            property: 'technical_articles',
                                            type: 'object',
                                            properties: [
                                                new OA\Property(property: 'total', type: 'integer', example: 25),
                                                new OA\Property(property: 'draft', type: 'integer', example: 4),
                                                new OA\Property(property: 'published', type: 'integer', example: 19),
                                                new OA\Property(property: 'hidden', type: 'integer', example: 2),
                                                new OA\Property(property: 'verified', type: 'integer', example: 16),
                                                new OA\Property(property: 'not_verified', type: 'integer', example: 9),
                                            ]
                                        ),
                                        new OA\Property(
                                            property: 'diseases',
                                            type: 'object',
                                            properties: [
                                                new OA\Property(property: 'total', type: 'integer', example: 10),
                                                new OA\Property(property: 'active', type: 'integer', example: 9),
                                                new OA\Property(property: 'inactive', type: 'integer', example: 1),
                                                new OA\Property(property: 'disease', type: 'integer', example: 5),
                                                new OA\Property(property: 'pest', type: 'integer', example: 3),
                                                new OA\Property(property: 'nutrient_deficiency', type: 'integer', example: 2),
                                                new OA\Property(property: 'other', type: 'integer', example: 0),
                                            ]
                                        ),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'diagnosis',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'total', type: 'integer', example: 18),
                                        new OA\Property(property: 'pending', type: 'integer', example: 5),
                                        new OA\Property(property: 'diagnosed', type: 'integer', example: 12),
                                        new OA\Property(property: 'rejected', type: 'integer', example: 1),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'qa',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(
                                            property: 'questions',
                                            type: 'object',
                                            properties: [
                                                new OA\Property(property: 'total', type: 'integer', example: 14),
                                                new OA\Property(property: 'pending', type: 'integer', example: 3),
                                                new OA\Property(property: 'answered', type: 'integer', example: 10),
                                                new OA\Property(property: 'closed', type: 'integer', example: 1),
                                            ]
                                        ),
                                        new OA\Property(
                                            property: 'answers',
                                            type: 'object',
                                            properties: [
                                                new OA\Property(property: 'total', type: 'integer', example: 21),
                                                new OA\Property(property: 'visible', type: 'integer', example: 19),
                                                new OA\Property(property: 'hidden', type: 'integer', example: 2),
                                                new OA\Property(property: 'accepted', type: 'integer', example: 8),
                                            ]
                                        ),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'market',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'prices', type: 'integer', example: 30),
                                        new OA\Property(property: 'active', type: 'integer', example: 28),
                                        new OA\Property(property: 'inactive', type: 'integer', example: 2),
                                        new OA\Property(property: 'domestic', type: 'integer', example: 24),
                                        new OA\Property(property: 'world', type: 'integer', example: 6),
                                        new OA\Property(property: 'manual', type: 'integer', example: 30),
                                        new OA\Property(property: 'api', type: 'integer', example: 0),
                                        new OA\Property(property: 'sheet', type: 'integer', example: 0),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'weather',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'cached_records', type: 'integer', example: 12),
                                        new OA\Property(property: 'cached_today', type: 'integer', example: 3),
                                    ]
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Chỉ quản trị viên mới được xem thống kê hệ thống'
            ),
            new OA\Response(
                response: 500,
                description: 'Lỗi hệ thống khi lấy thống kê'
            ),
        ]
    )]
    public function statistics(): void
    {
    }
}