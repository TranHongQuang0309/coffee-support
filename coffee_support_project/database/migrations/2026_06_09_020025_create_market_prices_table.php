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
        Schema::create('market_prices', function (Blueprint $table) {
            $table->id();

        $table->foreignId('created_by')
              ->nullable()
              ->constrained('users')
              ->nullOnDelete();

        $table->enum('market_scope', [
            'domestic',
            'world'
        ])->default('domestic');

        $table->string('province', 100)->nullable();

        $table->enum('coffee_type', [
            'robusta',
            'arabica',
            'mixed',
            'other'
        ])->default('robusta');

        $table->decimal('price', 12, 2);

        $table->string('unit', 50)->default('VND/kg');

        $table->date('price_date');

        $table->string('source', 150)->nullable();

        $table->string('source_url', 255)->nullable();

        $table->enum('source_type', [
            'manual',
            'api',
            'sheet'
        ])->default('manual');

        $table->timestamp('fetched_at')->nullable();

        $table->text('note')->nullable();

        $table->enum('status', [
            'active',
            'inactive'
        ])->default('active');

        $table->timestamps();

        $table->unique(
            ['market_scope', 'province', 'coffee_type', 'price_date'],
            'market_prices_unique_scope_province_type_date'
        );
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_prices');
    }
};
