<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->string('demo_video_url')->nullable()->after('support_channel');
            $table->timestamp('demo_video_attested_at')->nullable()->after('demo_video_url');
            $table->boolean('show_demo_video')->default(false)->after('demo_video_attested_at');
        });
    }

    public function down(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->dropColumn(['demo_video_url', 'demo_video_attested_at', 'show_demo_video']);
        });
    }
};
