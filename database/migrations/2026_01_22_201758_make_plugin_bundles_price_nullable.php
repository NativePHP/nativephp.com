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
        Schema::table('plugin_bundles', function (Blueprint $table) {
            $table->unsignedInteger('price')->nullable()->default(null)->change();
            $table->string('currency', 3)->default('USD')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugin_bundles', function (Blueprint $table) {
            $table->unsignedInteger('price')->nullable(false)->default(null)->change();
            $table->string('currency', 3)->default(null)->change();
        });
    }
};
