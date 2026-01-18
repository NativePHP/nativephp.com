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
        Schema::create('user_purchase_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_spent')->default(0);
            $table->timestamp('first_purchase_at')->nullable();
            $table->string('grandfathering_tier')->nullable();
            $table->timestamp('recalculated_at')->nullable();
            $table->timestamps();

            $table->index('grandfathering_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_purchase_history');
    }
};
