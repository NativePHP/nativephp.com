<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bundle_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plugin_bundle_id')->constrained()->cascadeOnDelete();
            $table->string('tier')->default('regular');
            $table->integer('amount');
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['plugin_bundle_id', 'is_active']);
            $table->index(['plugin_bundle_id', 'tier', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_prices');
    }
};
