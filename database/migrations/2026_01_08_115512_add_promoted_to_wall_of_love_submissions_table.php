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
        Schema::table('wall_of_love_submissions', function (Blueprint $table) {
            $table->boolean('promoted')->default(false)->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wall_of_love_submissions', function (Blueprint $table) {
            $table->dropColumn('promoted');
        });
    }
};
