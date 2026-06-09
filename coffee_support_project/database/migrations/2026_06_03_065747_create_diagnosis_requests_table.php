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
        Schema::create('diagnosis_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('coffee_farm_id')
                ->nullable()
                ->constrained('coffee_farms')
                ->nullOnDelete();

            $table->foreignId('predicted_disease_id')
                ->nullable()
                ->constrained('diseases')
                ->nullOnDelete();

            $table->foreignId('diagnosed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('image_url', 255);

            $table->text('symptom_description')->nullable();

            $table->decimal('confidence_score', 5, 2)->nullable();

            $table->text('diagnosis_note')->nullable();

            $table->text('recommendation')->nullable();

            $table->enum('status', [
                'pending',
                'diagnosed',
                'rejected'
            ])->default('pending');

            $table->timestamp('diagnosed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosis_requests');
    }
};
