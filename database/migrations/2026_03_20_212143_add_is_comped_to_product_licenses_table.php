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
        Schema::table('product_licenses', function (Blueprint $table) {
            $table->boolean('is_comped')->default(false)->after('currency')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_licenses', function (Blueprint $table) {
            $table->dropIndex(['is_comped']);
            $table->dropColumn('is_comped');
        });
    }
};
