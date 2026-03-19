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
        Schema::create('plugin_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plugin_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // submitted, resubmitted, approved, rejected
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable(); // rejection reason or other notes
            $table->foreignId('causer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['plugin_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugin_activities');
    }
};
