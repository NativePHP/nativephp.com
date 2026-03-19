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
            // Add a regular index on user_id first (MySQL needs an index for the foreign key)
            $table->index('user_id', 'plugin_licenses_user_id_index');
        });

        Schema::table('plugin_licenses', function (Blueprint $table) {
            // Now we can drop the unique constraint
            $table->dropUnique(['user_id', 'plugin_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugin_licenses', function (Blueprint $table) {
            $table->unique(['user_id', 'plugin_id']);
        });

        Schema::table('plugin_licenses', function (Blueprint $table) {
            $table->dropIndex('plugin_licenses_user_id_index');
        });
    }
};
