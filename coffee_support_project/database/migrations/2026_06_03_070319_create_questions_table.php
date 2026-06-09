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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

            $table->foreignId('coffee_farm_id')
              ->nullable()
              ->constrained('coffee_farms')
              ->nullOnDelete();

            $table->string('title', 200);

            $table->text('content');

            $table->string('image_url', 255)->nullable();

            $table->enum('status', [
                'pending',
                'answered',
                'closed',
                'hidden'
            ])->default('pending');

            $table->unsignedInteger('view_count')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
