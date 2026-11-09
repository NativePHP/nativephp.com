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
        Schema::create('docs_coverage_gaps', function (Blueprint $table) {
            $table->id();
            $table->string('platform');
            $table->string('category');
            $table->string('identifier');
            $table->boolean('documented');
            $table->string('source_path')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->unique(['platform', 'category', 'identifier']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docs_coverage_gaps');
    }
};
