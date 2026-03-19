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
        Schema::table('plugin_licenses', function (Blueprint $table) {
            $table->string('stripe_checkout_session_id')->nullable()->unique()->after('stripe_payment_intent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugin_licenses', function (Blueprint $table) {
            $table->dropColumn('stripe_checkout_session_id');
        });
    }
};
