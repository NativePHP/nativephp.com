<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->nullable()
                ->after('plugin_bundle_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedInteger('product_price_at_addition')
                ->nullable()
                ->after('bundle_price_at_addition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'product_price_at_addition']);
        });
    }
};
