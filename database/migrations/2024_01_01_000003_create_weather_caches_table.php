<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weather_caches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->decimal('temperature', 6, 2)->nullable();
            $table->decimal('precipitation', 6, 2)->nullable();
            $table->decimal('wind_speed', 6, 2)->nullable();
            $table->integer('weather_code')->nullable();
            $table->string('weather_description')->nullable();
            $table->decimal('storm_risk_score', 5, 2)->default(0);
            $table->timestamp('fetched_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_caches');
    }
};