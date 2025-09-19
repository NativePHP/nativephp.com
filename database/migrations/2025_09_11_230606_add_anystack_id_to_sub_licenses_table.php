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
        Schema::table('sub_licenses', function (Blueprint $table) {
            $table->uuid('anystack_id')->nullable()->after('parent_license_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_licenses', function (Blueprint $table) {
            $table->dropIndex(['anystack_id']);
            $table->dropColumn('anystack_id');
        });
    }
};
