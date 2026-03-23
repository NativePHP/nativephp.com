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
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->string('product')->nullable()->after('status');
            $table->string('issue_type')->nullable()->after('product');
            $table->json('metadata')->nullable()->after('issue_type');
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn(['product', 'issue_type', 'metadata']);
        });
    }
};
