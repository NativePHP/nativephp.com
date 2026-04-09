<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('plugins')
            ->where('status', 'pending')
            ->update(['status' => 'draft']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('plugins')
            ->where('status', 'draft')
            ->update(['status' => 'pending']);
    }
};
