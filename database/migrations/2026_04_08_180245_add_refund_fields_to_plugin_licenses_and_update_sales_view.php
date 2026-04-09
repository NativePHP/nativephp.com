<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS sales_view');

        Schema::table('plugin_licenses', function (Blueprint $table) {
            $table->timestamp('refunded_at')->nullable()->after('expires_at');
            $table->string('stripe_refund_id')->nullable()->after('refunded_at')->index();
            $table->foreignId('refunded_by')->nullable()->after('stripe_refund_id')
                ->constrained('users')->nullOnDelete();
        });

        DB::statement("
            CREATE VIEW sales_view AS
            SELECT
                CONCAT('pl_', pl.id) AS id,
                pl.user_id,
                p.name AS product_name,
                pb.name AS bundle_name,
                pl.price_paid,
                pl.currency,
                pl.is_grandfathered AS is_comped,
                pl.purchased_at,
                pl.created_at,
                pl.updated_at
            FROM plugin_licenses pl
            LEFT JOIN plugins p ON p.id = pl.plugin_id
            LEFT JOIN plugin_bundles pb ON pb.id = pl.plugin_bundle_id
            WHERE pl.refunded_at IS NULL

            UNION ALL

            SELECT
                CONCAT('pr_', pdl.id) AS id,
                pdl.user_id,
                pd.name AS product_name,
                NULL AS bundle_name,
                pdl.price_paid,
                pdl.currency,
                pdl.is_comped,
                pdl.purchased_at,
                pdl.created_at,
                pdl.updated_at
            FROM product_licenses pdl
            LEFT JOIN products pd ON pd.id = pdl.product_id
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS sales_view');

        DB::statement("
            CREATE VIEW sales_view AS
            SELECT
                CONCAT('pl_', pl.id) AS id,
                pl.user_id,
                p.name AS product_name,
                pb.name AS bundle_name,
                pl.price_paid,
                pl.currency,
                pl.is_grandfathered AS is_comped,
                pl.purchased_at,
                pl.created_at,
                pl.updated_at
            FROM plugin_licenses pl
            LEFT JOIN plugins p ON p.id = pl.plugin_id
            LEFT JOIN plugin_bundles pb ON pb.id = pl.plugin_bundle_id

            UNION ALL

            SELECT
                CONCAT('pr_', pdl.id) AS id,
                pdl.user_id,
                pd.name AS product_name,
                NULL AS bundle_name,
                pdl.price_paid,
                pdl.currency,
                pdl.is_comped,
                pdl.purchased_at,
                pdl.created_at,
                pdl.updated_at
            FROM product_licenses pdl
            LEFT JOIN products pd ON pd.id = pdl.product_id
        ");

        Schema::table('plugin_licenses', function (Blueprint $table) {
            $table->dropForeign(['refunded_by']);
            $table->dropIndex(['stripe_refund_id']);
            $table->dropColumn(['refunded_at', 'stripe_refund_id', 'refunded_by']);
        });
    }
};
