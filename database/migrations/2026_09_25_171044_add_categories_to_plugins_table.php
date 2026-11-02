<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Plugins can now be in more than one category. The old `category` column
     * is copied across and left in place for code that hasn't been deployed yet.
     */
    public function up(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->json('categories')->nullable()->after('category');
            $table->json('category_suggestions')->nullable()->after('categories');
        });

        DB::table('plugins')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->each(fn (string $category) => DB::table('plugins')
                ->where('category', $category)
                ->update(['categories' => json_encode([$category])]));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->dropColumn(['categories', 'category_suggestions']);
        });
    }
};
