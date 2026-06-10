<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoffeeFarm;
use App\Models\WeatherData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function getByFarm(Request $request, int $farmId): JsonResponse
    {
        $farm = CoffeeFarm::find($farmId);

        if (!$farm) {
            return $this->notFoundResponse('Không tìm thấy vườn cà phê.');
        }

        if ($this->isFarmer($request) && $farm->user_id !== $request->user()->id) {
            return $this->notFoundResponse('Không tìm thấy vườn cà phê hoặc bạn không có quyền truy cập.');
        }

        if (empty($farm->latitude) || empty($farm->longitude)) {
            return response()->json([
                'success' => false,
                'message' => 'Vườn cà phê chưa có tọa độ để lấy dữ liệu thời tiết.',
            ], 422);
        }

        $today = now('Asia/Bangkok')->toDateString();

        $cachedWeather = WeatherData::where('coffee_farm_id', $farm->id)
            ->whereDate('weather_date', $today)
            ->latest('fetched_at')
            ->first();

        if ($cachedWeather) {
            return response()->json([
                'success' => true,
                'message' => 'Lấy dữ liệu thời tiết từ cache thành công.',
                'data' => [
                    'is_cached' => true,
                    'farm' => $this->formatFarm($farm),
                    'source' => [
                        'name' => $cachedWeather->source,
                        'timezone' => 'Asia/Bangkok',
                    ],
                    'current' => [
                        'time' => optional($cachedWeather->fetched_at)->format('Y-m-d H:i:s'),
                        'temperature' => $cachedWeather->temperature_current,
                        'humidity' => $cachedWeather->humidity_current,
                        'precipitation' => $cachedWeather->precipitation_current,
                        'wind_speed' => $cachedWeather->wind_speed_current,
                        'weather_code' => $cachedWeather->weather_code_current,
                        'weather_text' => $cachedWeather->weather_text_current,
                    ],
                    'today_forecast' => [
                        'date' => optional($cachedWeather->weather_date)->toDateString(),
                        'temperature_min' => $cachedWeather->temperature_min,
                        'temperature_max' => $cachedWeather->temperature_max,
                        'precipitation_sum' => $cachedWeather->precipitation_sum,
                        'precipitation_probability' => $cachedWeather->precipitation_probability_max,
                        'wind_speed_max' => $cachedWeather->wind_speed_max,
                        'weather_code' => $cachedWeather->weather_code_daily,
                        'weather_text' => $cachedWeather->weather_text_daily,
                    ],
                    'daily_forecast' => $cachedWeather->raw_data['daily_forecast'] ?? [],
                    'hourly_forecast' => $cachedWeather->raw_data['hourly_forecast'] ?? [],
                    'warnings' => $cachedWeather->raw_data['warnings'] ?? [],
                    'fetched_at' => optional($cachedWeather->fetched_at)->format('Y-m-d H:i:s'),
                ],
            ]);
        }

        $response = Http::timeout(15)->get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $farm->latitude,
            'longitude' => $farm->longitude,

            'current' => [
                'temperature_2m',
                'relative_humidity_2m',
                'precipitation',
                'weather_code',
                'wind_speed_10m',
            ],

            'hourly' => [
                'temperature_2m',
                'relative_humidity_2m',
                'precipitation_probability',
                'precipitation',
                'wind_speed_10m',
                'weather_code',
            ],

            'daily' => [
                'temperature_2m_max',
                'temperature_2m_min',
                'precipitation_sum',
                'precipitation_probability_max',
                'wind_speed_10m_max',
                'weather_code',
            ],

            'timezone' => 'Asia/Bangkok',
            'forecast_days' => 7,
        ]);

        if ($response->failed()) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy dữ liệu thời tiết từ Open-Meteo.',
                'status_code' => $response->status(),
            ], 502);
        }

        $weather = $response->json();

        $current = $this->formatCurrentWeather($weather['current'] ?? []);
        $dailyForecast = $this->formatDailyForecast($weather['daily'] ?? []);
        $hourlyForecast = $this->formatHourlyForecast($weather['hourly'] ?? []);
        $warnings = $this->buildAgricultureWarnings($weather);

        $todayForecast = $dailyForecast[0] ?? [];

        $weatherCache = WeatherData::create([
            'coffee_farm_id' => $farm->id,
            'weather_date' => $today,

            'temperature_current' => $current['temperature'] ?? null,
            'temperature_min' => $todayForecast['temperature_min'] ?? null,
            'temperature_max' => $todayForecast['temperature_max'] ?? null,
            'humidity_current' => $current['humidity'] ?? null,

            'precipitation_current' => $current['precipitation'] ?? null,
            'precipitation_sum' => $todayForecast['precipitation_sum'] ?? null,
            'precipitation_probability_max' => $todayForecast['precipitation_probability'] ?? null,

            'wind_speed_current' => $current['wind_speed'] ?? null,
            'wind_speed_max' => $todayForecast['wind_speed_max'] ?? null,

            'weather_code_current' => $current['weather_code'] ?? null,
            'weather_code_daily' => $todayForecast['weather_code'] ?? null,
            'weather_text_current' => $current['weather_text'] ?? null,
            'weather_text_daily' => $todayForecast['weather_text'] ?? null,

            'source' => 'Open-Meteo',
            'raw_data' => [
                'current' => $current,
                'daily_forecast' => $dailyForecast,
                'hourly_forecast' => $hourlyForecast,
                'warnings' => $warnings,
                'open_meteo_raw' => $weather,
            ],
            'fetched_at' => now('Asia/Bangkok'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lấy dữ liệu thời tiết từ Open-Meteo và lưu cache thành công.',
            'data' => [
                'is_cached' => false,
                'farm' => $this->formatFarm($farm),
                'source' => [
                    'name' => 'Open-Meteo',
                    'timezone' => $weather['timezone'] ?? 'Asia/Bangkok',
                ],
                'current' => $current,
                'today_forecast' => $todayForecast,
                'daily_forecast' => $dailyForecast,
                'hourly_forecast' => $hourlyForecast,
                'warnings' => $warnings,
                'cache_id' => $weatherCache->id,
                'fetched_at' => optional($weatherCache->fetched_at)->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    private function formatFarm(CoffeeFarm $farm): array
    {
        return [
            'id' => $farm->id,
            'farm_name' => $farm->farm_name,
            'province' => $farm->province,
            'district' => $farm->district,
            'address' => $farm->address,
            'latitude' => $farm->latitude,
            'longitude' => $farm->longitude,
        ];
    }

    private function formatCurrentWeather(array $current): array
    {
        $weatherCode = $current['weather_code'] ?? null;

        return [
            'time' => $current['time'] ?? null,
            'temperature' => $current['temperature_2m'] ?? null,
            'humidity' => $current['relative_humidity_2m'] ?? null,
            'precipitation' => $current['precipitation'] ?? null,
            'wind_speed' => $current['wind_speed_10m'] ?? null,
            'weather_code' => $weatherCode,
            'weather_text' => $this->getWeatherText($weatherCode),
        ];
    }

    private function formatDailyForecast(array $daily): array
    {
        $result = [];

        $dates = $daily['time'] ?? [];

        foreach ($dates as $index => $date) {
            $weatherCode = $daily['weather_code'][$index] ?? null;

            $result[] = [
                'date' => $date,
                'temperature_max' => $daily['temperature_2m_max'][$index] ?? null,
                'temperature_min' => $daily['temperature_2m_min'][$index] ?? null,
                'precipitation_sum' => $daily['precipitation_sum'][$index] ?? null,
                'precipitation_probability' => $daily['precipitation_probability_max'][$index] ?? null,
                'wind_speed_max' => $daily['wind_speed_10m_max'][$index] ?? null,
                'weather_code' => $weatherCode,
                'weather_text' => $this->getWeatherText($weatherCode),
            ];
        }

        return $result;
    }

    private function formatHourlyForecast(array $hourly): array
    {
        $result = [];

        $times = $hourly['time'] ?? [];

        foreach ($times as $index => $time) {
            $weatherCode = $hourly['weather_code'][$index] ?? null;

            $result[] = [
                'time' => $time,
                'temperature' => $hourly['temperature_2m'][$index] ?? null,
                'humidity' => $hourly['relative_humidity_2m'][$index] ?? null,
                'precipitation_probability' => $hourly['precipitation_probability'][$index] ?? null,
                'precipitation' => $hourly['precipitation'][$index] ?? null,
                'wind_speed' => $hourly['wind_speed_10m'][$index] ?? null,
                'weather_code' => $weatherCode,
                'weather_text' => $this->getWeatherText($weatherCode),
            ];
        }

        return $result;
    }

    private function buildAgricultureWarnings(array $weather): array
    {
        $warnings = [];

        $currentHumidity = $weather['current']['relative_humidity_2m'] ?? null;
        $currentWindSpeed = $weather['current']['wind_speed_10m'] ?? null;

        $dailyRainProbability = $weather['daily']['precipitation_probability_max'][0] ?? null;
        $dailyRainSum = $weather['daily']['precipitation_sum'][0] ?? null;
        $dailyWindMax = $weather['daily']['wind_speed_10m_max'][0] ?? null;

        if ($currentHumidity !== null && $currentHumidity >= 85) {
            $warnings[] = 'Độ ẩm cao, cần theo dõi nguy cơ phát sinh nấm bệnh trên cây cà phê.';
        }

        if ($dailyRainProbability !== null && $dailyRainProbability >= 70) {
            $warnings[] = 'Khả năng mưa cao, cần cân nhắc lịch phun thuốc, bón phân hoặc thu hoạch.';
        }

        if ($dailyRainSum !== null && $dailyRainSum >= 10) {
            $warnings[] = 'Dự báo lượng mưa khá lớn, cần chú ý thoát nước cho vườn cà phê.';
        }

        if ($dailyWindMax !== null && $dailyWindMax >= 20) {
            $warnings[] = 'Gió mạnh, nên hạn chế phun thuốc bảo vệ thực vật.';
        }

        if ($currentWindSpeed !== null && $currentWindSpeed >= 20) {
            $warnings[] = 'Tốc độ gió hiện tại cao, không nên phun thuốc vào thời điểm này.';
        }

        return $warnings;
    }

    private function getWeatherText(?int $code): string
    {
        return match ($code) {
            0 => 'Trời quang',
            1 => 'Ít mây',
            2 => 'Có mây rải rác',
            3 => 'Nhiều mây',
            45, 48 => 'Sương mù',
            51 => 'Mưa phùn nhẹ',
            53 => 'Mưa phùn vừa',
            55 => 'Mưa phùn dày',
            61 => 'Mưa nhẹ',
            63 => 'Mưa vừa',
            65 => 'Mưa to',
            80 => 'Mưa rào nhẹ',
            81 => 'Mưa rào vừa',
            82 => 'Mưa rào mạnh',
            95 => 'Dông',
            96, 99 => 'Dông kèm mưa đá',
            default => 'Không xác định',
        };
    }

    private function isFarmer(Request $request): bool
    {
        return $request->user() && $request->user()->role === 'farmer';
    }

    private function notFoundResponse(string $message): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 404);
    }
}