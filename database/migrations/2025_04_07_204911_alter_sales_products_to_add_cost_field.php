<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\SaleProduct;
use App\Models\Product;

class AlterSalesProductsToAddCostField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_products', function (Blueprint $table) {
            // Schema::table('sales_products', function (Blueprint $table) {
            //     $table->double('cost')->default(0)->after('quantity');
            // });
    
            // Update the product cost in all sales with the current product value based on product id
            SaleProduct::query()->update([
                'cost' => Product::query()->where('id', SaleProduct::query()->value('product_id'))->value('cost') ?? 0
            ]);
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
            $table->dropColumn('cost');
        });
    }
}
