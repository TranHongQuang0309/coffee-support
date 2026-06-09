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
        Schema::create('diseases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('name', 150);
            $table->string('slug', 180)->unique();

            $table->enum('type', [
                'disease',
                'pest',
                'nutrient_deficiency',
                'other'
            ])->default('disease');

            $table->text('symptoms')->nullable();
            $table->text('causes')->nullable();
            $table->text('prevention')->nullable();
            $table->text('treatment')->nullable();

            $table->enum('severity', [
                'low',
                'medium',
                'high'
            ])->default('medium');

            $table->string('image_url', 255)->nullable();

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
        Schema::dropIfExists('diseases');
    }
};
