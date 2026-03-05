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
        if (Schema::hasColumn('teams', 'extra_seats')) {
            return;
        }

        Schema::table('teams', function (Blueprint $table) {
            $table->unsignedInteger('extra_seats')->default(0)->after('is_suspended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('extra_seats');
        });
    }
};
