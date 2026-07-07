<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->year('year');
            $table->decimal('gdp', 20, 2)->nullable();
            $table->decimal('inflation_rate', 8, 4)->nullable();
            $table->bigInteger('population')->nullable();
            $table->decimal('exports_value', 20, 2)->nullable();
            $table->decimal('imports_value', 20, 2)->nullable();
            $table->timestamps();

            $table->unique(['country_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_indicators');
    }
};