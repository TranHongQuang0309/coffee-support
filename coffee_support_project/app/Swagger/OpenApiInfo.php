<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Coffee Cultivation Support API',
    description: 'Tài liệu API cho hệ thống website hỗ trợ kỹ thuật nông nghiệp và canh tác cây cà phê khu vực Tây Nguyên.'
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000',
    description: 'Local development server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum Token',
    description: 'Nhập token theo dạng: Bearer {token}'
)]
class OpenApiInfo
{
}