<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_products', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
			$table->bigInteger('sale_id')->unsigned();
			$table->bigInteger('product_id')->unsigned();
			$table->integer('quantity')->unsigned();
			$table->double('price')->default(0);
			$table->double('discount')->default(0);
			$table->double('total')->default(0);
			$table->softDeletes();
            $table->timestamps();

			$table->foreign('sale_id')->references('id')->on('sales')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_products');
    }
}
