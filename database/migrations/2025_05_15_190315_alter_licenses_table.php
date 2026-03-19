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
        Schema::table('licenses', function (Blueprint $table) {
            $table->uuid('anystack_id')->nullable()->after('id');
            $table->uuid('key')->change();

            $table->unique('anystack_id');
            $table->unique('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropUnique(['anystack_id']);
            $table->dropUnique(['key']);

            $table->dropColumn(['anystack_id']);
            $table->string('key')->change();
        });
    }
};
