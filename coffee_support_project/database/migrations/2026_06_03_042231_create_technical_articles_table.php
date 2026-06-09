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
        Schema::create('technical_articles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('technical_category_id')
                ->constrained('technical_categories')
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title', 200);
            $table->string('slug', 220)->unique();

            $table->string('summary', 255)->nullable();
            $table->longText('content');

            $table->string('thumbnail_url', 255)->nullable();

            $table->enum('status', [
                'draft',
                'published',
                'hidden'
            ])->default('draft');

            $table->unsignedInteger('view_count')->default(0);

            $table->timestamp('published_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_articles');
    }
};
