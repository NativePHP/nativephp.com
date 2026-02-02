<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('feature_segments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not recreating - this table was from a removed plugin
    }
};
