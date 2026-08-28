<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('full_name');

            $table->string('phone', 30);

            $table->string('province_name');

            $table->string('district_name')
                ->nullable();

            $table->string('ward_name')
                ->nullable();

            $table->string('address_line');

            $table->string('label')
                ->nullable();

            $table->boolean('is_default')
                ->default(false);

            $table->timestamps();

            $table->index('user_id');
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};