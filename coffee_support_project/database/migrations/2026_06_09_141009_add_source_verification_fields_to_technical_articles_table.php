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
        Schema::table('technical_articles', function (Blueprint $table) {
            $table->enum('source_type', [
                'self_written',
                'external',
                'summarized',
                'expert_contributed',
            ])->default('self_written')->after('published_at');

            $table->string('source_name', 255)->nullable()->after('source_type');

            $table->string('source_url', 500)->nullable()->after('source_name');

            $table->boolean('is_verified')->default(false)->after('source_url');

            $table->foreignId('verified_by')
                ->nullable()
                ->after('is_verified')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('technical_articles', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);

            $table->dropColumn([
                'source_type',
                'source_name',
                'source_url',
                'is_verified',
                'verified_by',
                'verified_at',
            ]);
        });
    }
};