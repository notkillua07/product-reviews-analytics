<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropColumn('top_negative_reasons');
            $table->json('product_reasons')->nullable()->after('negative_count');
            $table->json('shipping_reasons')->nullable()->after('product_reasons');
        });
    }

    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropColumn(['product_reasons', 'shipping_reasons']);
            $table->json('top_negative_reasons')->nullable()->after('negative_count');
        });
    }
};
