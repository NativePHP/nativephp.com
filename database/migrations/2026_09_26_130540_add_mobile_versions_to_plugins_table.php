<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The lowest NativePHP Mobile release a plugin works with in each major
     * version, keyed by major version, e.g. {"3": "3.2.1", "4": "4.0"}.
     * Filled by `plugins:backfill-mobile-versions` and on every sync.
     */
    public function up(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->json('mobile_versions')->nullable()->after('mobile_min_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->dropColumn('mobile_versions');
        });
    }
};
