<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plugin_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plugin_price_id')->constrained()->cascadeOnDelete();
            $table->integer('price_at_addition')->comment('Price in cents when added to cart');
            $table->string('currency', 3)->default('USD');
            $table->timestamps();

            $table->unique(['cart_id', 'plugin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
