<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSalesProductsTableAddMercadolibreField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_products', function (Blueprint $table) {
			$table->boolean('mercadolibre')->default(0)->after('discount')->comment('Indica si el producto fue vendido por MercadoLibre');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_products', function (Blueprint $table) {
			$table->dropColumn('mercadolibre');
		});
    }
}
