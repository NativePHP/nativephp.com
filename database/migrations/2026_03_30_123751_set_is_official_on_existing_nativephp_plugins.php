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
            ->where('name', 'like', 'nativephp/%')
            ->update(['is_official' => true]);

        DB::table('plugins')
            ->where('name', 'not like', 'nativephp/%')
            ->update(['is_official' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback — is_official will be maintained by the model going forward
    }
};
