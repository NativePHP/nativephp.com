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
            $table->foreignId('plugin_bundle_id')
                ->nullable()
                ->after('plugin_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('plugin_licenses', function (Blueprint $table) {
            $table->dropForeign(['plugin_bundle_id']);
            $table->dropColumn('plugin_bundle_id');
        });
    }
};
