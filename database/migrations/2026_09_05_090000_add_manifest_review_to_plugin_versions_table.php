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
        Schema::table('plugin_versions', function (Blueprint $table) {
            $table->text('release_notes_html')->nullable()->after('release_notes');
            $table->json('manifest_permissions')->nullable()->after('release_notes_html');
            $table->boolean('permissions_expanded')->default(false)->after('manifest_permissions');
            $table->boolean('requires_review')->default(false)->after('permissions_expanded');
            $table->foreignId('approved_by')->nullable()->after('requires_review')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            $table->index('requires_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plugin_versions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['release_notes_html', 'manifest_permissions', 'permissions_expanded', 'requires_review', 'approved_at']);
        });
    }
};
