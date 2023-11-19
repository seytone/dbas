<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
			$table->bigInteger('category_id')->unsigned();
			$table->bigInteger('brand_id')->unsigned();
			$table->enum('group', ['perpetual', 'annual', 'hardware'])->default('hardware');
			$table->enum('type', ['hardware', 'software'])->default('hardware');
			$table->string('code');
			$table->string('title');
			$table->string('description')->nullable()->default(null);
			$table->double('cost')->default(0);
			$table->double('price')->default(0);
			$table->softDeletes();
            $table->timestamps();

			$table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('brand_id')->references('id')->on('brands')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
