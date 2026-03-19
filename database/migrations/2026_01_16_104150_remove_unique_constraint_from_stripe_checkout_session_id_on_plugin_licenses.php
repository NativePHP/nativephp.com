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
            // Drop the unique constraint - multiple licenses can share the same checkout session (cart purchases)
            $table->dropUnique(['stripe_checkout_session_id']);
        });

        Schema::table('plugin_licenses', function (Blueprint $table) {
            // Add a regular index for query performance
            $table->index('stripe_checkout_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugin_licenses', function (Blueprint $table) {
            $table->dropIndex(['stripe_checkout_session_id']);
        });

        Schema::table('plugin_licenses', function (Blueprint $table) {
            $table->unique('stripe_checkout_session_id');
        });
    }
};
