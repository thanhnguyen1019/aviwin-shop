<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')
                ->default(true)
                ->after('role');

            $table->timestamp('blocked_at')
                ->nullable()
                ->after('is_active');

            $table->text('blocked_reason')
                ->nullable()
                ->after('blocked_at');

            $table->index('is_active');
            $table->index('blocked_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['blocked_at']);

            $table->dropColumn([
                'is_active',
                'blocked_at',
                'blocked_reason',
            ]);
        });
    }
};