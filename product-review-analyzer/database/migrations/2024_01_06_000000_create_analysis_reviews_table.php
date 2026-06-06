<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('review_order_id');   // sequential id from API (1-based)
            $table->text('text');
            $table->string('label', 10);                  // positive | negative
            $table->decimal('confidence', 7, 4)->nullable();
            $table->string('confidence_level', 10)->nullable(); // high | medium | low
            $table->timestamps();

            $table->index('analysis_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_reviews');
    }
};
