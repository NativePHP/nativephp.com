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
        Schema::create('plugin_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plugin_license_id')->constrained()->cascadeOnDelete();
            $table->foreignId('developer_account_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('gross_amount');
            $table->unsignedInteger('platform_fee');
            $table->unsignedInteger('developer_amount');
            $table->string('stripe_transfer_id')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('transferred_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('stripe_transfer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugin_payouts');
    }
};
