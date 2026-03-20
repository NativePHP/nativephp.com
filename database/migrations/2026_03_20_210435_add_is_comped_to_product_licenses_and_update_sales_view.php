<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_licenses', function (Blueprint $table) {
            $table->boolean('is_comped')->default(false)->after('currency')->index();
        });

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
                0 AS is_comped,
                pdl.purchased_at,
                pdl.created_at,
                pdl.updated_at
            FROM product_licenses pdl
            LEFT JOIN products pd ON pd.id = pdl.product_id
        ");

        Schema::table('product_licenses', function (Blueprint $table) {
            $table->dropIndex(['is_comped']);
            $table->dropColumn('is_comped');
        });
    }
};
