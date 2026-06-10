<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class DiseaseApi
{
    #[OA\Get(
        path: '/api/diseases',
        summary: 'Lấy danh sách sâu bệnh',
        description: 'Lấy danh sách bệnh hại, sâu hại hoặc tình trạng thiếu dinh dưỡng trên cây cà phê. Có thể lọc theo loại, mức độ nghiêm trọng, trạng thái và từ khóa.',
        security: [['bearerAuth' => []]],
        tags: ['Diseases'],
        parameters: [
            new OA\Parameter(
                name: 'type',
                description: 'Loại vấn đề trên cây cà phê',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['disease', 'pest', 'nutrient_deficiency', 'other']
                ),
                example: 'disease'
            ),
            new OA\Parameter(
                name: 'severity',
                description: 'Mức độ nghiêm trọng',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['low', 'medium', 'high']
                ),
                example: 'medium'
            ),
            new OA\Parameter(
                name: 'status',
                description: 'Trạng thái hiển thị',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['active', 'inactive']
                ),
                example: 'active'
            ),
            new OA\Parameter(
                name: 'keyword',
                description: 'Từ khóa tìm kiếm theo tên, triệu chứng, nguyên nhân hoặc cách xử lý',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'gỉ sắt'
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy danh sách sâu bệnh thành công'
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
        path: '/api/diseases',
        summary: 'Tạo sâu bệnh mới',
        description: 'Admin tạo mới thông tin bệnh hại, sâu hại hoặc vấn đề thiếu dinh dưỡng trên cây cà phê.',
        security: [['bearerAuth' => []]],
        tags: ['Diseases'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'type', 'symptoms'],
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        example: 'Bệnh gỉ sắt trên cây cà phê'
                    ),
                    new OA\Property(
                        property: 'slug',
                        type: 'string',
                        example: 'benh-gi-sat-tren-cay-ca-phe'
                    ),
                    new OA\Property(
                        property: 'type',
                        type: 'string',
                        enum: ['disease', 'pest', 'nutrient_deficiency', 'other'],
                        example: 'disease'
                    ),
                    new OA\Property(
                        property: 'symptoms',
                        type: 'string',
                        example: 'Lá xuất hiện các đốm màu vàng cam, mặt dưới lá có lớp bột màu cam, lá rụng sớm.'
                    ),
                    new OA\Property(
                        property: 'causes',
                        type: 'string',
                        example: 'Do nấm gây bệnh phát triển mạnh trong điều kiện ẩm độ cao, vườn rậm rạp và thiếu thông thoáng.'
                    ),
                    new OA\Property(
                        property: 'prevention',
                        type: 'string',
                        example: 'Tỉa cành tạo thông thoáng, vệ sinh vườn, bón phân cân đối và theo dõi thường xuyên trong mùa mưa.'
                    ),
                    new OA\Property(
                        property: 'treatment',
                        type: 'string',
                        example: 'Loại bỏ lá bệnh nặng, sử dụng thuốc phòng trừ nấm phù hợp theo khuyến cáo kỹ thuật.'
                    ),
                    new OA\Property(
                        property: 'severity',
                        type: 'string',
                        enum: ['low', 'medium', 'high'],
                        example: 'medium'
                    ),
                    new OA\Property(
                        property: 'image_url',
                        type: 'string',
                        example: 'diseases/benh-gi-sat-ca-phe.jpg'
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
                description: 'Tạo thông tin sâu bệnh thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Chỉ quản trị viên mới được tạo sâu bệnh'
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
        path: '/api/diseases/{id}',
        summary: 'Lấy chi tiết sâu bệnh',
        description: 'Lấy chi tiết thông tin một bệnh hại, sâu hại hoặc tình trạng thiếu dinh dưỡng theo ID.',
        security: [['bearerAuth' => []]],
        tags: ['Diseases'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID sâu bệnh',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy chi tiết sâu bệnh thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy thông tin sâu bệnh'
            ),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: '/api/diseases/{id}',
        summary: 'Cập nhật sâu bệnh',
        description: 'Admin cập nhật thông tin bệnh hại, sâu hại hoặc tình trạng thiếu dinh dưỡng.',
        security: [['bearerAuth' => []]],
        tags: ['Diseases'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID sâu bệnh',
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
                        property: 'name',
                        type: 'string',
                        example: 'Cập nhật bệnh gỉ sắt trên cây cà phê'
                    ),
                    new OA\Property(
                        property: 'slug',
                        type: 'string',
                        example: 'cap-nhat-benh-gi-sat-tren-cay-ca-phe'
                    ),
                    new OA\Property(
                        property: 'type',
                        type: 'string',
                        enum: ['disease', 'pest', 'nutrient_deficiency', 'other'],
                        example: 'disease'
                    ),
                    new OA\Property(
                        property: 'symptoms',
                        type: 'string',
                        example: 'Lá có đốm vàng cam, mặt dưới lá có bột màu cam, cây sinh trưởng kém.'
                    ),
                    new OA\Property(
                        property: 'causes',
                        type: 'string',
                        example: 'Nấm bệnh phát triển trong điều kiện mưa nhiều, ẩm độ cao và vườn thiếu thông thoáng.'
                    ),
                    new OA\Property(
                        property: 'prevention',
                        type: 'string',
                        example: 'Tạo tán hợp lý, thu gom lá bệnh, quản lý dinh dưỡng và độ ẩm vườn.'
                    ),
                    new OA\Property(
                        property: 'treatment',
                        type: 'string',
                        example: 'Áp dụng biện pháp phòng trừ tổng hợp, dùng thuốc theo khuyến cáo của chuyên gia.'
                    ),
                    new OA\Property(
                        property: 'severity',
                        type: 'string',
                        enum: ['low', 'medium', 'high'],
                        example: 'high'
                    ),
                    new OA\Property(
                        property: 'image_url',
                        type: 'string',
                        example: 'diseases/cap-nhat-gi-sat.jpg'
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
                description: 'Cập nhật thông tin sâu bệnh thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Chỉ quản trị viên mới được cập nhật sâu bệnh'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy thông tin sâu bệnh'
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
        path: '/api/diseases/{id}',
        summary: 'Xóa sâu bệnh',
        description: 'Admin xóa thông tin sâu bệnh khỏi hệ thống.',
        security: [['bearerAuth' => []]],
        tags: ['Diseases'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID sâu bệnh',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Xóa thông tin sâu bệnh thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Chỉ quản trị viên mới được xóa sâu bệnh'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy thông tin sâu bệnh'
            ),
        ]
    )]
    public function destroy(): void
    {
    }
}