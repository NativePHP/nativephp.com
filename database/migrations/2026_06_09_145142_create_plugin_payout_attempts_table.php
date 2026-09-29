<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugin_payout_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plugin_payout_id')->constrained()->cascadeOnDelete();
            $table->boolean('succeeded')->default(false);
            $table->string('charge_id')->nullable();
            $table->string('stripe_transfer_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('plugin_payout_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugin_payout_attempts');
    }
};
