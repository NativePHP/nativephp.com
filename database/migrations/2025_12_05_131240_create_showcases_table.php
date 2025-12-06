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
        Schema::create('showcases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable();
            $table->json('screenshots')->nullable();
            $table->boolean('has_mobile')->default(false);
            $table->boolean('has_desktop')->default(false);
            $table->string('play_store_url')->nullable();
            $table->string('app_store_url')->nullable();
            $table->string('windows_download_url')->nullable();
            $table->string('macos_download_url')->nullable();
            $table->string('linux_download_url')->nullable();
            $table->boolean('certified_nativephp')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['approved_at', 'has_mobile', 'has_desktop']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showcases');
    }
};
