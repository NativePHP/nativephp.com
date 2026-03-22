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
        Schema::table('developer_accounts', function (Blueprint $table) {
            $table->timestamp('accepted_plugin_terms_at')->nullable()->after('onboarding_completed_at');
            $table->string('plugin_terms_version')->nullable()->after('accepted_plugin_terms_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('developer_accounts', function (Blueprint $table) {
            $table->dropColumn(['accepted_plugin_terms_at', 'plugin_terms_version']);
        });
    }
};
