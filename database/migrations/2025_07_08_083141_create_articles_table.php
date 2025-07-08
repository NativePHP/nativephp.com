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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('author_id')
                ->nullable();

            $table->foreign('author_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->string('slug')->unique();
            $table->string('title', 255);
            $table->string('excerpt', 400);
            $table->text('content');

            $table->timestamp('published_at')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
