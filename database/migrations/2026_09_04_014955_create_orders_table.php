<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('code')
                ->unique();

            $table->string('status', 50)
                ->default('pending');

            $table->string('payment_status', 50)
                ->default('unpaid');

            $table->string('payment_method', 50)
                ->nullable();

            $table->decimal('subtotal', 15, 2)
                ->default(0);

            $table->decimal('discount_amount', 15, 2)
                ->default(0);

            $table->decimal('shipping_fee', 15, 2)
                ->default(0);

            $table->decimal('total_amount', 15, 2)
                ->default(0);

            // Snapshot người nhận
            $table->string('receiver_name');

            $table->string('receiver_phone', 30);

            $table->string('province_name');

            $table->string('district_name')
                ->nullable();

            $table->string('ward_name')
                ->nullable();

            $table->string('address_line');

            $table->text('note')
                ->nullable();

            $table->timestamp('ordered_at')
                ->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('ordered_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};