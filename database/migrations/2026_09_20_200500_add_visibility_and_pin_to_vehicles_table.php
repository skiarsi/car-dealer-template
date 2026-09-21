<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->boolean('is_visible')->default(true)->after('featured');
            $table->boolean('is_pinned')->default(false)->after('is_visible');
            $table->index(['is_visible', 'is_pinned']);
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex(['is_visible', 'is_pinned']);
            $table->dropColumn(['is_visible', 'is_pinned']);
        });
    }
};
