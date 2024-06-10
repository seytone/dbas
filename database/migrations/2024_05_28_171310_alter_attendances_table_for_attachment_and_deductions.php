<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAttendancesTableForAttachmentAndDeductions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
	{
		Schema::table('attendances', function (Blueprint $table) {
			$table->double('missing')->default(0)->after('extra');
			$table->double('total')->default(0)->after('missing');
			$table->text('payment_evidence')->nullable()->default(null)->after('payment_date');
		});
	}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('attendances', function (Blueprint $table) {
			$table->dropColumn('missing');
			$table->dropColumn('total');
			$table->dropColumn('payment_evidence');
		});
    }
}
