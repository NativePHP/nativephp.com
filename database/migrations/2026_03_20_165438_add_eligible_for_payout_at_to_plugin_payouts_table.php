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
        Schema::table('plugin_payouts', function (Blueprint $table) {
            $table->timestamp('eligible_for_payout_at')->nullable()->after('transferred_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugin_payouts', function (Blueprint $table) {
            $table->dropColumn('eligible_for_payout_at');
        });
    }
};
