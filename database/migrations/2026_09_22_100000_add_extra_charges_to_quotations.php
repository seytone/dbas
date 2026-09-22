<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraChargesToQuotations extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            // Conceptos adicionales con nombre libre (ej. "ITBMS Panamá",
            // "Impuesto de importación"). Se guardan como array JSON de
            // {label, amount} y suman al final del total, igual que el
            // flete — sin afectar la base imponible ni el IVA.
            $table->json('extra_charges')->nullable()->after('freight');
        });
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('extra_charges');
        });
    }
}
