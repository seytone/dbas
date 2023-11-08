<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
			$table->bigInteger('user_id')->unsigned();
			$table->double('commission_1')->default(1)->comment('Comisión sobre licencias perpétuas');
			$table->double('commission_2')->default(1)->comment('Comisión sobre suscripciones anuales');
			$table->double('commission_3')->default(1)->comment('Comisión sobre hadrware y otros');
			$table->double('commission_4')->default(50)->comment('Comisión sobre servicios');
			$table->softDeletes();
            $table->timestamps();

			$table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sellers');
    }
}
