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
        Schema::table('plugin_prices', function (Blueprint $table) {
            $table->string('tier')->default('regular')->after('plugin_id');
            $table->index(['plugin_id', 'tier', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugin_prices', function (Blueprint $table) {
            $table->dropIndex(['plugin_id', 'tier', 'is_active']);
            $table->dropColumn('tier');
        });
    }
};
