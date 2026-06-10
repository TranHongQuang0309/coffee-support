<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class DiagnosisRequestApi
{
    #[OA\Get(
        path: '/api/diagnosis-requests',
        summary: 'Lấy danh sách yêu cầu chẩn đoán',
        description: 'Farmer xem danh sách yêu cầu chẩn đoán của mình. Expert/Admin có thể xem danh sách yêu cầu để xử lý.',
        security: [['bearerAuth' => []]],
        tags: ['Diagnosis Requests'],
        parameters: [
            new OA\Parameter(
                name: 'status',
                description: 'Lọc theo trạng thái yêu cầu chẩn đoán',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: ['pending', 'diagnosed', 'rejected']
                ),
                example: 'pending'
            ),
            new OA\Parameter(
                name: 'coffee_farm_id',
                description: 'ID vườn cà phê cần lọc',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
            new OA\Parameter(
                name: 'predicted_disease_id',
                description: 'ID sâu bệnh được dự đoán/chẩn đoán',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 1
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
                description: 'Lấy danh sách yêu cầu chẩn đoán thành công'
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
        path: '/api/diagnosis-requests',
        summary: 'Tạo yêu cầu chẩn đoán',
        description: 'Farmer gửi hình ảnh và mô tả triệu chứng để yêu cầu chẩn đoán sâu bệnh trên cây cà phê.',
        security: [['bearerAuth' => []]],
        tags: ['Diagnosis Requests'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['coffee_farm_id', 'image_url', 'symptom_description'],
                properties: [
                    new OA\Property(
                        property: 'coffee_farm_id',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'image_url',
                        type: 'string',
                        example: 'diagnosis-requests/la-ca-phe-bi-benh.jpg'
                    ),
                    new OA\Property(
                        property: 'symptom_description',
                        type: 'string',
                        example: 'Lá cà phê xuất hiện các đốm vàng cam ở mặt dưới, cây có dấu hiệu rụng lá.'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Tạo yêu cầu chẩn đoán thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Không có quyền tạo yêu cầu chẩn đoán'
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
        path: '/api/diagnosis-requests/{id}',
        summary: 'Lấy chi tiết yêu cầu chẩn đoán',
        description: 'Lấy chi tiết một yêu cầu chẩn đoán sâu bệnh theo ID.',
        security: [['bearerAuth' => []]],
        tags: ['Diagnosis Requests'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID yêu cầu chẩn đoán',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy chi tiết yêu cầu chẩn đoán thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy yêu cầu chẩn đoán'
            ),
        ]
    )]
    public function show(): void
    {
    }

    #[OA\Put(
        path: '/api/diagnosis-requests/{id}',
        summary: 'Cập nhật kết quả chẩn đoán',
        description: 'Expert/Admin cập nhật kết quả chẩn đoán, bệnh dự đoán, độ tin cậy, ghi chú và khuyến nghị xử lý.',
        security: [['bearerAuth' => []]],
        tags: ['Diagnosis Requests'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID yêu cầu chẩn đoán',
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
                        property: 'predicted_disease_id',
                        type: 'integer',
                        example: 1
                    ),
                    new OA\Property(
                        property: 'confidence_score',
                        type: 'number',
                        format: 'float',
                        example: 87.5,
                        description: 'Độ tin cậy của kết quả chẩn đoán, tính theo phần trăm'
                    ),
                    new OA\Property(
                        property: 'diagnosis_note',
                        type: 'string',
                        example: 'Triệu chứng phù hợp với bệnh gỉ sắt trên cây cà phê.'
                    ),
                    new OA\Property(
                        property: 'recommendation',
                        type: 'string',
                        example: 'Cần tỉa cành tạo thông thoáng, loại bỏ lá bệnh nặng và áp dụng biện pháp phòng trừ nấm theo khuyến cáo.'
                    ),
                    new OA\Property(
                        property: 'status',
                        type: 'string',
                        enum: ['pending', 'diagnosed', 'rejected'],
                        example: 'diagnosed'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cập nhật kết quả chẩn đoán thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Không có quyền cập nhật yêu cầu chẩn đoán'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy yêu cầu chẩn đoán'
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
        path: '/api/diagnosis-requests/{id}',
        summary: 'Xóa yêu cầu chẩn đoán',
        description: 'Farmer có thể xóa yêu cầu chẩn đoán của mình nếu còn ở trạng thái pending. Admin có thể xóa theo quyền quản trị.',
        security: [['bearerAuth' => []]],
        tags: ['Diagnosis Requests'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'ID yêu cầu chẩn đoán',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Xóa yêu cầu chẩn đoán thành công'
            ),
            new OA\Response(
                response: 401,
                description: 'Chưa đăng nhập hoặc token không hợp lệ'
            ),
            new OA\Response(
                response: 403,
                description: 'Không có quyền xóa yêu cầu chẩn đoán'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy yêu cầu chẩn đoán'
            ),
        ]
    )]
    public function destroy(): void
    {
    }
}