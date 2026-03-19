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
            $table->string('repository_url')->nullable()->after('name');
            $table->string('webhook_secret', 64)->nullable()->unique()->after('repository_url');
            $table->longText('readme_html')->nullable()->after('android_version');
            $table->json('composer_data')->nullable()->after('readme_html');
            $table->json('nativephp_data')->nullable()->after('composer_data');
            $table->timestamp('last_synced_at')->nullable()->after('nativephp_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugins', function (Blueprint $table) {
            $table->dropColumn([
                'repository_url',
                'webhook_secret',
                'readme_html',
                'composer_data',
                'nativephp_data',
                'last_synced_at',
            ]);
        });
    }
};
