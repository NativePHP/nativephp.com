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
        Schema::table('developer_accounts', function (Blueprint $table) {
            $table->unsignedTinyInteger('payout_percentage')->default(70)->after('payout_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('developer_accounts', function (Blueprint $table) {
            $table->dropColumn('payout_percentage');
        });
    }
};
