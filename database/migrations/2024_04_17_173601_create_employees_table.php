<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('number')->unique()->comment('Número de empleado');
			$table->string('pin')->unique()->comment('Cédula de identidad');
            $table->string('name');
            $table->string('lastname');
            $table->string('email')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);
            $table->string('department')->nullable()->default(null)->comment('Departamento');
            $table->string('position')->nullable()->default(null)->comment('Cargo');
            $table->double('salary')->nullable()->default(null)->commnet('Salario');
            $table->boolean('active')->default(true);
			$table->softDeletes();
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
