<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class AuthApi
{
    #[OA\Post(
        path: '/api/login',
        summary: 'Đăng nhập',
        description: 'Đăng nhập bằng email và password, trả về access token Sanctum.',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        example: 'admin@coffee-support.test'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        example: '12345678'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Đăng nhập thành công',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Đăng nhập thành công.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'token', type: 'string', example: '1|abcxyz...'),
                                new OA\Property(
                                    property: 'user',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 1),
                                        new OA\Property(property: 'full_name', type: 'string', example: 'Admin User'),
                                        new OA\Property(property: 'email', type: 'string', example: 'admin@coffee-support.test'),
                                        new OA\Property(property: 'role', type: 'string', example: 'admin'),
                                    ]
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Dữ liệu không hợp lệ'
            ),
            new OA\Response(
                response: 401,
                description: 'Sai email hoặc mật khẩu'
            ),
        ]
    )]
    public function login(): void
    {
    }

    #[OA\Post(
        path: '/api/register',
        summary: 'Đăng ký tài khoản farmer',
        description: 'Tạo tài khoản người trồng cà phê. Mặc định role là farmer.',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['full_name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'full_name', type: 'string', example: 'Nguyễn Văn A'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'farmer01@example.com'),
                    new OA\Property(property: 'phone', type: 'string', example: '0901234567'),
                    new OA\Property(property: 'password', type: 'string', example: '12345678'),
                    new OA\Property(property: 'password_confirmation', type: 'string', example: '12345678'),
                    new OA\Property(property: 'province', type: 'string', example: 'Đắk Lắk'),
                    new OA\Property(property: 'district', type: 'string', example: 'Cư Mgar'),
                    new OA\Property(property: 'address', type: 'string', example: 'Xã Ea Tul'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Đăng ký thành công'
            ),
            new OA\Response(
                response: 422,
                description: 'Dữ liệu không hợp lệ'
            ),
        ]
    )]
    public function register(): void
    {
    }

    #[OA\Get(
        path: '/api/me',
        summary: 'Lấy thông tin người dùng hiện tại',
        description: 'Trả về thông tin user đang đăng nhập.',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy thông tin người dùng thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
        ]
    )]
    public function me(): void
    {
    }

    #[OA\Post(
        path: '/api/logout',
        summary: 'Đăng xuất',
        description: 'Xóa token hiện tại của người dùng.',
        security: [['bearerAuth' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Đăng xuất thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
        ]
    )]
    public function logout(): void
    {
    }
}