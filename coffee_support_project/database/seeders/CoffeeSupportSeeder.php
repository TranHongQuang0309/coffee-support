<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\CoffeeFarm;
use App\Models\CultivationLog;
use App\Models\TechnicalCategory;
use App\Models\TechnicalArticle;
use App\Models\Disease;
use App\Models\DiagnosisRequest;
use App\Models\Question;
use App\Models\Answer;
use App\Models\MarketPrice;
use App\Models\WeatherData;

class CoffeeSupportSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Users
        |--------------------------------------------------------------------------
        */

        $admin = User::create([
            'full_name' => 'Quản trị viên hệ thống',
            'email' => 'admin@coffee-support.test',
            'phone' => '0900000001',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'status' => 'active',
            'avatar_url' => 'avatars/admin.png',
            'province' => 'Đắk Lắk',
            'district' => 'Buôn Ma Thuột',
            'address' => 'Trung tâm Buôn Ma Thuột',
        ]);

        $expert = User::create([
            'full_name' => 'Chuyên gia nông nghiệp',
            'email' => 'expert@coffee-support.test',
            'phone' => '0900000002',
            'password' => Hash::make('12345678'),
            'role' => 'expert',
            'status' => 'active',
            'avatar_url' => 'avatars/expert.png',
            'province' => 'Đắk Lắk',
            'district' => 'Buôn Ma Thuột',
            'address' => 'Phường Tân Lợi',
        ]);

        $farmer = User::create([
            'full_name' => 'Người trồng cà phê mẫu',
            'email' => 'farmer@coffee-support.test',
            'phone' => '0900000003',
            'password' => Hash::make('12345678'),
            'role' => 'farmer',
            'status' => 'active',
            'avatar_url' => 'avatars/farmer.png',
            'province' => 'Đắk Lắk',
            'district' => 'Cư Mgar',
            'address' => 'Xã Ea Tul',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. Coffee Farms
        |--------------------------------------------------------------------------
        */

        $farm = CoffeeFarm::create([
            'user_id' => $farmer->id,
            'farm_name' => 'Vườn cà phê Ea Tul',
            'area' => 2.5,
            'coffee_type' => $this->enumValue('coffee_farms', 'coffee_type', [
                'robusta',
                'arabica',
                'mixed',
                'other',
            ]),
            'planting_year' => 2018,
            'province' => 'Đắk Lắk',
            'district' => 'Cư Mgar',
            'address' => 'Xã Ea Tul, huyện Cư Mgar',
            'latitude' => 12.6667,
            'longitude' => 108.0500,
            'description' => 'Vườn cà phê Robusta đang trong giai đoạn kinh doanh.',
            'status' => $this->enumValue('coffee_farms', 'status', [
                'active',
                'enabled',
                'available',
                'inactive',
            ]),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. Cultivation Logs
        |--------------------------------------------------------------------------
        */

        CultivationLog::create([
            'user_id' => $farmer->id,
            'coffee_farm_id' => $farm->id,
            'log_date' => now()->subDays(7)->toDateString(),
            'activity_type' => $this->enumValue('cultivation_logs', 'activity_type', [
                'fertilizing',
                'watering',
                'spraying',
                'other',
            ]),
            'title' => 'Bón phân đợt đầu mùa mưa',
            'description' => 'Bón phân NPK kết hợp phân hữu cơ quanh gốc cà phê.',
            'cost' => 1200000,
            'image_url' => 'cultivation/fertilizing-sample.jpg',
            'status' => $this->enumValue('cultivation_logs', 'status', [
                'completed',
                'planned',
                'cancelled',
            ]),
        ]);

        CultivationLog::create([
            'user_id' => $farmer->id,
            'coffee_farm_id' => $farm->id,
            'log_date' => now()->subDays(3)->toDateString(),
            'activity_type' => $this->enumValue('cultivation_logs', 'activity_type', [
                'watering',
                'fertilizing',
                'other',
            ]),
            'title' => 'Tưới nước bổ sung',
            'description' => 'Tưới nước cho khu vực cây có dấu hiệu thiếu ẩm.',
            'cost' => 300000,
            'image_url' => 'cultivation/watering-sample.jpg',
            'status' => $this->enumValue('cultivation_logs', 'status', [
                'completed',
                'planned',
                'cancelled',
            ]),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4. Technical Categories
        |--------------------------------------------------------------------------
        */

        $categoryCare = TechnicalCategory::create([
            'name' => 'Kỹ thuật chăm sóc',
            'slug' => 'ky-thuat-cham-soc',
            'description' => 'Các bài viết hướng dẫn chăm sóc cây cà phê.',
            'status' => $this->enumValue('technical_categories', 'status', [
                'active',
                'published',
                'approved',
                'inactive',
            ]),
        ]);

        $categoryDisease = TechnicalCategory::create([
            'name' => 'Phòng trừ sâu bệnh',
            'slug' => 'phong-tru-sau-benh',
            'description' => 'Thông tin về sâu bệnh và biện pháp xử lý.',
            'status' => $this->enumValue('technical_categories', 'status', [
                'active',
                'published',
                'approved',
                'inactive',
            ]),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 5. Technical Articles
        |--------------------------------------------------------------------------
        */

        TechnicalArticle::create([
            'technical_category_id' => $categoryCare->id,
            'created_by' => $admin->id,
            'title' => 'Kỹ thuật bón phân cho cây cà phê Robusta',
            'slug' => 'ky-thuat-bon-phan-cho-cay-ca-phe-robusta',
            'summary' => 'Hướng dẫn nguyên tắc bón phân hợp lý cho cà phê Robusta.',
            'content' => 'Cây cà phê cần được bón phân cân đối theo từng giai đoạn sinh trưởng. Người trồng cần chú ý lượng phân, thời điểm bón và độ ẩm đất.',
            'thumbnail_url' => 'articles/bon-phan-ca-phe.jpg',
            'status' => $this->enumValue('technical_articles', 'status', [
                'published',
                'active',
                'approved',
                'draft',
            ]),
            'view_count' => 25,
            'published_at' => now(),
        ]);

        TechnicalArticle::create([
            'technical_category_id' => $categoryDisease->id,
            'created_by' => $admin->id,
            'title' => 'Dấu hiệu nhận biết bệnh gỉ sắt trên cây cà phê',
            'slug' => 'dau-hieu-nhan-biet-benh-gi-sat-tren-cay-ca-phe',
            'summary' => 'Bệnh gỉ sắt là một bệnh phổ biến trên cây cà phê, thường xuất hiện trên lá.',
            'content' => 'Bệnh gỉ sắt thường gây ra các vết màu vàng cam ở mặt dưới lá. Nếu không xử lý kịp thời, cây suy yếu và giảm năng suất.',
            'thumbnail_url' => 'articles/benh-gi-sat-ca-phe.jpg',
            'status' => $this->enumValue('technical_articles', 'status', [
                'published',
                'active',
                'approved',
                'draft',
            ]),
            'view_count' => 18,
            'published_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 6. Diseases
        |--------------------------------------------------------------------------
        */

        $rustDisease = Disease::create([
            'created_by' => $admin->id,
            'name' => 'Bệnh gỉ sắt cà phê',
            'slug' => 'benh-gi-sat-ca-phe',
            'type' => $this->enumValue('diseases', 'type', [
                'disease',
                'pest',
                'nutrient_deficiency',
                'other',
            ]),
            'symptoms' => 'Xuất hiện đốm vàng cam ở mặt dưới lá, lá rụng sớm.',
            'causes' => 'Do nấm gây bệnh, thường phát triển mạnh trong điều kiện ẩm độ cao.',
            'prevention' => 'Tỉa cành thông thoáng, vệ sinh vườn, bón phân cân đối.',
            'treatment' => 'Sử dụng thuốc phòng trừ nấm theo khuyến cáo kỹ thuật.',
            'severity' => $this->enumValue('diseases', 'severity', [
                'medium',
                'high',
                'low',
                'severe',
            ]),
            'image_url' => 'diseases/benh-gi-sat-ca-phe.jpg',
            'status' => $this->enumValue('diseases', 'status', [
                'active',
                'published',
                'approved',
                'inactive',
            ]),
        ]);

        Disease::create([
            'created_by' => $admin->id,
            'name' => 'Rệp sáp hại cà phê',
            'slug' => 'rep-sap-hai-ca-phe',
            'type' => $this->enumValue('diseases', 'type', [
                'pest',
                'disease',
                'nutrient_deficiency',
                'other',
            ]),
            'symptoms' => 'Xuất hiện lớp sáp trắng ở chùm quả, cành non hoặc rễ.',
            'causes' => 'Do rệp sáp chích hút nhựa cây, thường xuất hiện khi vườn rậm rạp.',
            'prevention' => 'Cắt tỉa cành, quản lý kiến, giữ vườn thông thoáng.',
            'treatment' => 'Xử lý ổ rệp sớm và dùng thuốc phù hợp khi mật độ cao.',
            'severity' => $this->enumValue('diseases', 'severity', [
                'high',
                'medium',
                'low',
                'severe',
            ]),
            'image_url' => 'diseases/rep-sap-hai-ca-phe.jpg',
            'status' => $this->enumValue('diseases', 'status', [
                'active',
                'published',
                'approved',
                'inactive',
            ]),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 7. Diagnosis Requests
        |--------------------------------------------------------------------------
        */

        DiagnosisRequest::create([
            'user_id' => $farmer->id,
            'coffee_farm_id' => $farm->id,
            'predicted_disease_id' => $rustDisease->id,
            'diagnosed_by' => $expert->id,
            'image_url' => 'diagnosis/sample-rust-disease.jpg',
            'symptom_description' => 'Lá cà phê xuất hiện nhiều đốm vàng cam ở mặt dưới.',
            'confidence_score' => 87.50,
            'diagnosis_note' => 'Triệu chứng phù hợp với bệnh gỉ sắt trên cây cà phê.',
            'recommendation' => 'Cần tỉa cành tạo thông thoáng, theo dõi mức độ bệnh và xử lý bằng thuốc phù hợp.',
            'status' => $this->enumValue('diagnosis_requests', 'status', [
                'completed',
                'diagnosed',
                'resolved',
                'reviewed',
                'approved',
                'pending',
            ]),
            'diagnosed_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 8. Questions
        |--------------------------------------------------------------------------
        */

        $question = Question::create([
            'user_id' => $farmer->id,
            'coffee_farm_id' => $farm->id,
            'title' => 'Lá cà phê bị vàng và rụng nhiều là do đâu?',
            'content' => 'Vườn cà phê của tôi gần đây có nhiều lá vàng, một số lá có đốm màu cam. Tôi nên xử lý như thế nào?',
            'image_url' => 'questions/la-ca-phe-bi-vang.jpg',
            'status' => $this->enumValue('questions', 'status', [
                'approved',
                'published',
                'active',
                'answered',
                'pending',
            ]),
            'view_count' => 12,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 9. Answers
        |--------------------------------------------------------------------------
        */

        Answer::create([
            'question_id' => $question->id,
            'user_id' => $expert->id,
            'content' => 'Triệu chứng bạn mô tả có thể liên quan đến bệnh gỉ sắt. Bạn nên kiểm tra mặt dưới lá, tỉa cành tạo thông thoáng và theo dõi mức độ lây lan.',
            'image_url' => 'answers/huong-dan-xu-ly-gi-sat.jpg',
            'status' => $this->enumValue('answers', 'status', [
                'approved',
                'published',
                'active',
                'pending',
            ]),
            'is_accepted' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 10. Market Prices
        |--------------------------------------------------------------------------
        */

        MarketPrice::create([
            'created_by' => $admin->id,
            'market_scope' => $this->enumValue('market_prices', 'market_scope', [
                'domestic',
                'local',
                'world',
            ]),
            'province' => 'Đắk Lắk',
            'coffee_type' => $this->enumValue('market_prices', 'coffee_type', [
                'robusta',
                'mixed',
                'arabica',
                'other',
            ]),
            'price' => 120000,
            'unit' => 'VND/kg',
            'price_date' => now()->toDateString(),
            'source' => 'Admin nhập mẫu',
            'source_url' => 'https://example.com/gia-ca-phe-dak-lak',
            'source_type' => $this->enumValue('market_prices', 'source_type', [
                'manual',
                'api',
                'sheet',
            ]),
            'fetched_at' => now(),
            'note' => 'Dữ liệu mẫu phục vụ demo hệ thống.',
            'status' => $this->enumValue('market_prices', 'status', [
                'active',
                'published',
                'approved',
                'inactive',
            ]),
        ]);

        MarketPrice::create([
            'created_by' => $admin->id,
            'market_scope' => $this->enumValue('market_prices', 'market_scope', [
                'domestic',
                'local',
                'world',
            ]),
            'province' => 'Gia Lai',
            'coffee_type' => $this->enumValue('market_prices', 'coffee_type', [
                'robusta',
                'mixed',
                'arabica',
                'other',
            ]),
            'price' => 119500,
            'unit' => 'VND/kg',
            'price_date' => now()->toDateString(),
            'source' => 'Admin nhập mẫu',
            'source_url' => 'https://example.com/gia-ca-phe-gia-lai',
            'source_type' => $this->enumValue('market_prices', 'source_type', [
                'manual',
                'api',
                'sheet',
            ]),
            'fetched_at' => now(),
            'note' => 'Dữ liệu mẫu phục vụ demo hệ thống.',
            'status' => $this->enumValue('market_prices', 'status', [
                'active',
                'published',
                'approved',
                'inactive',
            ]),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 11. Weather Data
        |--------------------------------------------------------------------------
        */

        WeatherData::create([
            'coffee_farm_id' => $farm->id,
            'weather_date' => now()->toDateString(),
            'temperature_min' => 22.5,
            'temperature_max' => 31.2,
            'temperature_current' => 28.4,
            'humidity' => 78,
            'precipitation' => 3.5,
            'precipitation_probability' => 60,
            'wind_speed' => 8.2,
            'weather_code' => 61,
            'source' => 'Open-Meteo',
            'fetched_at' => now(),
            'data_type' => $this->enumValue('weather_data', 'data_type', [
                'forecast',
                'current',
                'historical',
            ]),
        ]);
    }

    private function enumValue(string $table, string $column, array $preferredValues): string
{
    $columnInfo = DB::selectOne(
        "
        SELECT 
            COLUMN_TYPE as column_type,
            COLUMN_DEFAULT as column_default
        FROM information_schema.COLUMNS
        WHERE 
            TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = ?
            AND COLUMN_NAME = ?
        ",
        [$table, $column]
    );

    if (!$columnInfo) {
        return $preferredValues[0];
    }

    $type = $columnInfo->column_type ?? '';
    $default = $columnInfo->column_default ?? null;

    preg_match_all("/'((?:[^'\\\\]|\\\\.)*)'/", $type, $matches);

    $allowedValues = $matches[1] ?? [];

    if (empty($allowedValues)) {
        return $preferredValues[0];
    }

    foreach ($preferredValues as $value) {
        if (in_array($value, $allowedValues, true)) {
            return $value;
        }
    }

    if ($default && in_array($default, $allowedValues, true)) {
        return $default;
    }

    return $allowedValues[0];
}
}
