<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coffee_farms', function (Blueprint $table) {
            $table->id();

        $table->foreignId('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

        $table->string('farm_name', 150);

        $table->decimal('area', 8, 2)->nullable();

        $table->enum('coffee_type', [
            'robusta',
            'arabica',
            'mixed',
            'other'
        ])->default('robusta');

        $table->year('planting_year')->nullable();

        $table->string('province', 100)->nullable();
        $table->string('district', 100)->nullable();
        $table->string('address', 255)->nullable();

        $table->decimal('latitude', 10, 7)->nullable();
        $table->decimal('longitude', 10, 7)->nullable();

        $table->text('description')->nullable();

        $table->enum('status', [
            'active',
            'inactive'
        ])->default('active');

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coffee_farms');
    }
};
