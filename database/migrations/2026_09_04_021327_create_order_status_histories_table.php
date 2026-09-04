<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('from_status', 50)
                ->nullable();

            $table->string('to_status', 50);

            $table->text('note')
                ->nullable();

            $table->unsignedBigInteger('changed_by')
                ->nullable();

            $table->string('changed_by_type', 50)
                ->default('system');

            $table->timestamps();

            $table->index('order_id');
            $table->index('to_status');
            $table->index('changed_by');
            $table->index('changed_by_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};