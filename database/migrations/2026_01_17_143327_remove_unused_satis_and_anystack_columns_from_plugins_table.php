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
        Schema::table('plugins', function (Blueprint $table) {
            $table->dropIndex(['satis_included']);
            $table->dropColumn(['satis_included', 'anystack_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->boolean('satis_included')->default(false)->after('is_official');
            $table->string('anystack_id')->nullable()->after('type');

            $table->index('satis_included');
        });
    }
};
