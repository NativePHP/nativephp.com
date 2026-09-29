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
        Schema::create('missed_plugin_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plugin_idea_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('term', 500);
            $table->string('type')->nullable();
            $table->text('use_case')->nullable();
            $table->float('existing_idea_probability')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missed_plugin_searches');
    }
};
