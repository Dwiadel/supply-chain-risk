<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('source')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->integer('positive_score')->default(0);
            $table->integer('negative_score')->default(0);
            $table->enum('sentiment', ['Positive', 'Neutral', 'Negative'])->default('Neutral');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_articles');
    }
};