<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddManualCalculationToQuotations extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            // true = quotation predates the automatic BCV formula (calculated manually).
            $table->boolean('manual_calculation')->default(false)->after('bcv_rate');
        });

        // Flag every quotation that already exists at deploy time as legacy/manual,
        // since none of them used the automatic rate formula.
        DB::table('quotations')->update(['manual_calculation' => true]);
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('manual_calculation');
        });
    }
}
