<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
			$table->bigInteger('employee_id')->unsigned();
			$table->string('year');
			$table->string('month');
			$table->double('hours')->default(0);
			$table->double('extra')->default(0);
			$table->enum('payment', ['pending', 'completed'])->default('pending')->comment('Estado del pago = pendiente | completado');
			$table->date('payment_date')->nullable()->default(null)->comment('Fecha de pago');
			$table->text('comments')->nullable();
			$table->softDeletes();
			$table->timestamps();

			$table->foreign('employee_id')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
