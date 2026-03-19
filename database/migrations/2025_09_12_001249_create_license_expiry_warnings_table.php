<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_expiry_warnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained()->cascadeOnDelete();
            $table->integer('warning_days'); // 30, 7, 1, etc.
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->index(['license_id', 'warning_days', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_expiry_warnings');
    }
};
