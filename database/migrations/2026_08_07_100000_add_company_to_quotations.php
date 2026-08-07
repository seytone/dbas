<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyToQuotations extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            // 've' (Distribuidora Bit de Activación y Servicios, C.A.) |
            // 'us' (Distribuidora Bit Corp). The emitting company chosen at
            // creation time; used to pick the PDF header. Fixed after save.
            $table->string('company', 4)->default('ve')->after('client_address');
        });
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('company');
        });
    }
}
