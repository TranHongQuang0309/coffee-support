<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

class WeatherApi
{
    #[OA\Get(
        path: '/api/weather/farms/{farm_id}',
        summary: 'Lấy dữ liệu thời tiết theo vườn cà phê',
        description: 'Lấy thông tin thời tiết hiện tại và dự báo theo vườn cà phê dựa trên latitude/longitude. Hệ thống ưu tiên trả dữ liệu cache trong bảng weather_data nếu đã có dữ liệu trong ngày, nếu chưa có sẽ gọi Open-Meteo API và lưu cache.',
        security: [['bearerAuth' => []]],
        tags: ['Weather'],
        parameters: [
            new OA\Parameter(
                name: 'farm_id',
                description: 'ID vườn cà phê cần lấy dữ liệu thời tiết',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lấy dữ liệu thời tiết thành công',
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
                            example: 'Lấy dữ liệu thời tiết thành công.'
                        ),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'farm',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 1),
                                        new OA\Property(property: 'farm_name', type: 'string', example: 'Vườn cà phê Ea Tul'),
                                        new OA\Property(property: 'province', type: 'string', example: 'Đắk Lắk'),
                                        new OA\Property(property: 'district', type: 'string', example: 'Cư Mgar'),
                                        new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 12.6667),
                                        new OA\Property(property: 'longitude', type: 'number', format: 'float', example: 108.05),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'source',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'name', type: 'string', example: 'Open-Meteo'),
                                        new OA\Property(property: 'timezone', type: 'string', example: 'Asia/Bangkok'),
                                        new OA\Property(property: 'is_cached', type: 'boolean', example: true),
                                        new OA\Property(property: 'fetched_at', type: 'string', format: 'date-time', example: '2026-06-10 08:00:00'),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'current',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'temperature', type: 'number', format: 'float', example: 26.7),
                                        new OA\Property(property: 'humidity', type: 'integer', example: 78),
                                        new OA\Property(property: 'precipitation', type: 'number', format: 'float', example: 0),
                                        new OA\Property(property: 'wind_speed', type: 'number', format: 'float', example: 4.9),
                                        new OA\Property(property: 'weather_code', type: 'integer', example: 3),
                                        new OA\Property(property: 'weather_text', type: 'string', example: 'Nhiều mây'),
                                    ]
                                ),
                                new OA\Property(
                                    property: 'daily_forecast',
                                    type: 'array',
                                    items: new OA\Items(
                                        type: 'object',
                                        properties: [
                                            new OA\Property(property: 'date', type: 'string', format: 'date', example: '2026-06-10'),
                                            new OA\Property(property: 'temperature_min', type: 'number', format: 'float', example: 22.4),
                                            new OA\Property(property: 'temperature_max', type: 'number', format: 'float', example: 30.1),
                                            new OA\Property(property: 'precipitation_sum', type: 'number', format: 'float', example: 3.5),
                                            new OA\Property(property: 'precipitation_probability_max', type: 'integer', example: 65),
                                            new OA\Property(property: 'wind_speed_max', type: 'number', format: 'float', example: 12.5),
                                            new OA\Property(property: 'weather_code', type: 'integer', example: 61),
                                            new OA\Property(property: 'weather_text', type: 'string', example: 'Mưa nhẹ'),
                                        ]
                                    )
                                ),
                                new OA\Property(
                                    property: 'hourly_forecast',
                                    type: 'array',
                                    items: new OA\Items(
                                        type: 'object',
                                        properties: [
                                            new OA\Property(property: 'time', type: 'string', example: '2026-06-10 09:00'),
                                            new OA\Property(property: 'temperature', type: 'number', format: 'float', example: 27.2),
                                            new OA\Property(property: 'humidity', type: 'integer', example: 76),
                                            new OA\Property(property: 'precipitation_probability', type: 'integer', example: 40),
                                            new OA\Property(property: 'precipitation', type: 'number', format: 'float', example: 0.2),
                                            new OA\Property(property: 'wind_speed', type: 'number', format: 'float', example: 5.1),
                                            new OA\Property(property: 'weather_code', type: 'integer', example: 3),
                                            new OA\Property(property: 'weather_text', type: 'string', example: 'Nhiều mây'),
                                        ]
                                    )
                                ),
                                new OA\Property(
                                    property: 'warnings',
                                    type: 'array',
                                    items: new OA\Items(type: 'string'),
                                    example: ['Khả năng mưa cao, cần chú ý thoát nước cho vườn cà phê.']
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
                description: 'Không có quyền xem dữ liệu thời tiết của vườn này'
            ),
            new OA\Response(
                response: 404,
                description: 'Không tìm thấy vườn cà phê'
            ),
            new OA\Response(
                response: 422,
                description: 'Vườn cà phê chưa có latitude/longitude'
            ),
            new OA\Response(
                response: 500,
                description: 'Không thể lấy dữ liệu thời tiết từ nguồn bên ngoài'
            ),
        ]
    )]
    public function getWeatherByFarm(): void
    {
    }
}