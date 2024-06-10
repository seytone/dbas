<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAttendanceRecordsTableForDeductions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_records', function (Blueprint $table) {
			$table->double('missing')->default(0)->after('extra_time');
			$table->double('missing_time')->default(0)->after('missing');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('attendance_records', function (Blueprint $table) {
			$table->dropColumn('missing');
			$table->dropColumn('missing_time');
		});
    }
}
