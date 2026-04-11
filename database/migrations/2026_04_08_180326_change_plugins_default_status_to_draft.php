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
        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support ALTER COLUMN DEFAULT directly,
            // and Schema::change() fails when views reference the table.
            // The default is only used for new inserts; we set status explicitly in code.
            return;
        }

        DB::statement("ALTER TABLE plugins ALTER COLUMN status SET DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE plugins ALTER COLUMN status SET DEFAULT 'pending'");
    }
};
