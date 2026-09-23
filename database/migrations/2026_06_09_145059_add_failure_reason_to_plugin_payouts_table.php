<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plugin_payouts', function (Blueprint $table) {
            $table->text('failure_reason')->nullable()->after('status');
            $table->unsignedInteger('attempt_count')->default(0)->after('failure_reason');
            $table->timestamp('last_attempted_at')->nullable()->after('attempt_count');
        });
    }

    public function down(): void
    {
        Schema::table('plugin_payouts', function (Blueprint $table) {
            $table->dropColumn(['failure_reason', 'attempt_count', 'last_attempted_at']);
        });
    }
};
