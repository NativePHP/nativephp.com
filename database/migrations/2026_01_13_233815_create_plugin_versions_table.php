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
        Schema::create('plugin_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plugin_id')->constrained()->cascadeOnDelete();
            $table->string('version');
            $table->string('tag_name');
            $table->text('release_notes')->nullable();
            $table->string('github_release_id')->nullable();
            $table->string('commit_sha')->nullable();
            $table->string('storage_path')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->boolean('is_packaged')->default(false);
            $table->timestamp('packaged_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['plugin_id', 'version']);
            $table->index('is_packaged');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugin_versions');
    }
};
