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
            $table->text('promoted_testimonial')->nullable()->after('promoted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wall_of_love_submissions', function (Blueprint $table) {
            $table->dropColumn('promoted_testimonial');
        });
    }
};
