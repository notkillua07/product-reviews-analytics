<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_reasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['product', 'shipping']);
            $table->string('reason');
            $table->unsignedInteger('count')->default(0);
            $table->string('severity', 20)->nullable();         // critical | moderate | minor
            $table->tinyInteger('severity_score')->nullable();  // 1–5
            $table->text('severity_explanation')->nullable();
            $table->json('review_ids')->nullable();             // [2, 4, 22, …]
            $table->timestamps();

            $table->index(['analysis_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_reasons');
    }
};
