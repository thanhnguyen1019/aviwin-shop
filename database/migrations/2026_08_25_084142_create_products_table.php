<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('name');

            $table->string('slug')
                ->unique();

            $table->string('code')
                ->unique();

            $table->string('short_description')
                ->nullable();

            $table->longText('description')
                ->nullable();

            $table->decimal('price', 15, 2)
                ->default(0);

            $table->decimal('sale_price', 15, 2)
                ->nullable();

            $table->string('thumbnail')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->boolean('is_featured')
                ->default(false);

            $table->integer('sort_order')
                ->default(0);

            $table->softDeletes();

            $table->timestamps();

            $table->index('category_id');
            $table->index('brand_id');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};