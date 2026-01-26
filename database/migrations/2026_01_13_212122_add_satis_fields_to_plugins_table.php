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
        Schema::table('plugins', function (Blueprint $table) {
            $table->boolean('is_official')->default(false)->after('type');
            $table->foreignId('developer_account_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->boolean('satis_included')->default(false)->after('is_official');

            $table->index('is_official');
            $table->index('satis_included');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->dropForeign(['developer_account_id']);
            $table->dropColumn(['is_official', 'developer_account_id', 'satis_included']);
        });
    }
};
