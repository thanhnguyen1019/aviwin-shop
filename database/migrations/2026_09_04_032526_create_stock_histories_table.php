<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnDelete();

            $table->string('type', 30);

            $table->integer('quantity_change');

            $table->integer('stock_before');

            $table->integer('stock_after');

            $table->text('reason');

            $table->unsignedBigInteger('changed_by')
                ->nullable();

            $table->timestamps();

            $table->index('product_variant_id');
            $table->index('type');
            $table->index('changed_by');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};