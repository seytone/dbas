<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_records', function (Blueprint $table) {
			$table->bigIncrements('id')->unsigned();
			$table->bigInteger('attendance_id')->unsigned();
			$table->date('date');
			$table->string('day');
			$table->time('entry')->nullable();
			$table->time('exit')->nullable();
			$table->double('hours')->default(0);
			$table->double('extra')->default(0);
			$table->double('extra_time')->default(0);
			$table->tinyInteger('apply')->default(0);
			$table->text('comments')->nullable();
			$table->softDeletes();
			$table->timestamps();

			$table->foreign('attendance_id')->references('id')->on('attendances')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_records');
    }
}
