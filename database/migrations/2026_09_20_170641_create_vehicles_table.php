<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->restrictOnDelete();
            $table->foreignId('vehicle_model_id')->constrained()->restrictOnDelete();
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('year');
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('mileage')->nullable();
            $table->string('engine_type');
            $table->string('transmission');
            $table->unsignedTinyInteger('seats');
            $table->string('color')->nullable();
            $table->string('body_type');
            $table->string('drivetrain')->nullable();
            $table->unsignedTinyInteger('doors')->nullable();
            $table->text('description')->nullable();
            $table->text('description_es')->nullable();
            $table->string('status')->default('available');
            $table->boolean('featured')->default(false);
            $table->string('vin')->nullable()->unique();
            $table->timestamps();

            $table->index(['status', 'featured']);
            $table->index(['year', 'price']);
            $table->index(['engine_type', 'seats', 'body_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
