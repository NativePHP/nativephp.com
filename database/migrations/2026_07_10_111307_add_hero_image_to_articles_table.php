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
        Schema::table('articles', function (Blueprint $table) {
            $table->string('hero_image')->nullable()->after('og_image');
            $table->string('card_image')->nullable()->after('hero_image');
            $table->json('og_image_crop')->nullable()->after('card_image');
            $table->json('card_image_crop')->nullable()->after('og_image_crop');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['hero_image', 'card_image', 'og_image_crop', 'card_image_crop']);
        });
    }
};
