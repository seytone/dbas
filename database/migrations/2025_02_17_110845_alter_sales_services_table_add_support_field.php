<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSalesServicesTableAddSupportField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_services', function (Blueprint $table) {
			$table->boolean('support')->default(0)->after('discount')->comment('Indica si el servicio incluye soporte técnico');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_services', function (Blueprint $table) {
			$table->dropColumn('support');
		});
    }
}
