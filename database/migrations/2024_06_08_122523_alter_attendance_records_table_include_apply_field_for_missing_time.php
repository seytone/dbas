<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAttendanceRecordsTableIncludeApplyFieldForMissingTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_records', function (Blueprint $table) {
			$table->renameColumn('apply', 'extra_apply')->comment('Indica si aplica el pago extra')->after('extra_time');
			$table->tinyInteger('missing_apply')->default(0)->after('missing_time')->comment('Indica si aplica la deducción por tiempo faltante');
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
			$table->renameColumn('extra_apply', 'apply');
			$table->dropColumn('missing_apply');
		});
    }
}
