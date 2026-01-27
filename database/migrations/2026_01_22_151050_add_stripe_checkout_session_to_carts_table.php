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
        Schema::table('carts', function (Blueprint $table) {
            $table->string('stripe_checkout_session_id')->nullable()->after('session_id')->index();
            $table->timestamp('completed_at')->nullable()->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['stripe_checkout_session_id', 'completed_at']);
        });
    }
};
