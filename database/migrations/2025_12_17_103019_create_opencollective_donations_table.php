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
        Schema::create('opencollective_donations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('webhook_id')->unique();
            $table->unsignedBigInteger('order_id')->unique();
            $table->string('order_idv2')->nullable();
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('USD');
            $table->string('interval')->nullable();
            $table->unsignedBigInteger('from_collective_id');
            $table->string('from_collective_name')->nullable();
            $table->string('from_collective_slug')->nullable();
            $table->json('raw_payload');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index(['claimed_at', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opencollective_donations');
    }
};
