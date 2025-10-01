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
        Schema::create('sub_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_license_id')->constrained('licenses')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->uuid('key')->unique();
            $table->boolean('is_suspended')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['parent_license_id', 'is_suspended']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_licenses');
    }
};
