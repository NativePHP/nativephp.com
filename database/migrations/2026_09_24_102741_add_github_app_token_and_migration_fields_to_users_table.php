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
        Schema::table('users', function (Blueprint $table) {
            $table->text('github_refresh_token')->nullable()->after('github_auth_type');
            $table->timestamp('github_token_expires_at')->nullable()->after('github_refresh_token');
            $table->timestamp('github_app_migration_notified_at')->nullable()->after('github_token_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['github_refresh_token', 'github_token_expires_at', 'github_app_migration_notified_at']);
        });
    }
};
