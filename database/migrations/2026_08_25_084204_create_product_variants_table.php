<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('color_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('size_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('sku')
                ->unique();

            $table->decimal('price', 15, 2)
                ->nullable();

            $table->decimal('sale_price', 15, 2)
                ->nullable();

            $table->integer('stock')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index('product_id');
            $table->index('color_id');
            $table->index('size_id');
            $table->index('is_active');

            $table->unique(
                ['product_id', 'color_id', 'size_id'],
                'product_color_size_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};