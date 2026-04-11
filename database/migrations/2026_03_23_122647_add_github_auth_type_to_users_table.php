<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('github_auth_type')->nullable()->after('github_token');
        });

        // Backfill existing users with OAuth tokens
        DB::table('users')
            ->whereNotNull('github_token')
            ->update(['github_auth_type' => 'oauth']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('github_auth_type');
        });
    }
};
