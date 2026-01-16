<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('plugin_id')->nullable()->change();
            $table->foreignId('plugin_price_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('plugin_id')->nullable(false)->change();
            $table->foreignId('plugin_price_id')->nullable(false)->change();
        });
    }
};
