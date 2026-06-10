<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\CoffeeFarm;
use App\Models\CultivationLog;
use App\Models\DiagnosisRequest;
use App\Models\Disease;
use App\Models\MarketPrice;
use App\Models\Question;
use App\Models\TechnicalArticle;
use App\Models\TechnicalCategory;
use App\Models\User;
use App\Models\WeatherData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function statistics(Request $request): JsonResponse
    {
        if (!$this->isAdmin($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ quản trị viên mới được xem thống kê hệ thống.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy thống kê hệ thống thành công.',
            'data' => [
                'users' => $this->getUserStatistics(),
                'farms' => $this->getFarmStatistics(),
                'cultivation' => $this->getCultivationStatistics(),
                'content' => $this->getContentStatistics(),
                'diagnosis' => $this->getDiagnosisStatistics(),
                'qa' => $this->getQuestionAnswerStatistics(),
                'market' => $this->getMarketStatistics(),
                'weather' => $this->getWeatherStatistics(),
            ],
        ]);
    }

    private function getUserStatistics(): array
    {
        return [
            'total' => User::count(),
            'farmers' => User::where('role', 'farmer')->count(),
            'experts' => User::where('role', 'expert')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'active' => User::where('status', 'active')->count(),
            'locked' => User::where('status', 'locked')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
        ];
    }

    private function getFarmStatistics(): array
    {
        return [
            'total' => CoffeeFarm::count(),
        ];
    }

    private function getCultivationStatistics(): array
    {
        return [
            'logs' => CultivationLog::count(),
        ];
    }

    private function getContentStatistics(): array
    {
        return [
            'technical_categories' => TechnicalCategory::count(),
            'technical_articles' => [
                'total' => TechnicalArticle::count(),
                'draft' => TechnicalArticle::where('status', 'draft')->count(),
                'published' => TechnicalArticle::where('status', 'published')->count(),
                'hidden' => TechnicalArticle::where('status', 'hidden')->count(),
                'verified' => TechnicalArticle::where('is_verified', true)->count(),
                'not_verified' => TechnicalArticle::where('is_verified', false)->count(),
            ],
            'diseases' => [
                'total' => Disease::count(),
                'active' => Disease::where('status', 'active')->count(),
                'inactive' => Disease::where('status', 'inactive')->count(),
                'disease' => Disease::where('type', 'disease')->count(),
                'pest' => Disease::where('type', 'pest')->count(),
                'nutrient_deficiency' => Disease::where('type', 'nutrient_deficiency')->count(),
                'other' => Disease::where('type', 'other')->count(),
            ],
        ];
    }

    private function getDiagnosisStatistics(): array
    {
        return [
            'total' => DiagnosisRequest::count(),
            'pending' => DiagnosisRequest::where('status', 'pending')->count(),
            'diagnosed' => DiagnosisRequest::where('status', 'diagnosed')->count(),
            'rejected' => DiagnosisRequest::where('status', 'rejected')->count(),
        ];
    }

    private function getQuestionAnswerStatistics(): array
    {
        return [
            'questions' => [
                'total' => Question::count(),
                'pending' => Question::where('status', 'pending')->count(),
                'answered' => Question::where('status', 'answered')->count(),
                'closed' => Question::where('status', 'closed')->count(),
            ],
            'answers' => [
                'total' => Answer::count(),
                'visible' => Answer::where('status', 'visible')->count(),
                'hidden' => Answer::where('status', 'hidden')->count(),
                'accepted' => Answer::where('is_accepted', true)->count(),
            ],
        ];
    }

    private function getMarketStatistics(): array
    {
        return [
            'prices' => [
                'total' => MarketPrice::count(),
                'active' => MarketPrice::where('status', 'active')->count(),
                'inactive' => MarketPrice::where('status', 'inactive')->count(),
                'domestic' => MarketPrice::where('market_scope', 'domestic')->count(),
                'world' => MarketPrice::where('market_scope', 'world')->count(),
                'manual' => MarketPrice::where('source_type', 'manual')->count(),
                'api' => MarketPrice::where('source_type', 'api')->count(),
                'sheet' => MarketPrice::where('source_type', 'sheet')->count(),
            ],
        ];
    }

    private function getWeatherStatistics(): array
    {
        return [
            'cached_records' => WeatherData::count(),
            'cached_today' => WeatherData::whereDate('weather_date', now('Asia/Bangkok')->toDateString())->count(),
        ];
    }

    private function isAdmin(Request $request): bool
    {
        return $request->user() && $request->user()->role === 'admin';
    }
}