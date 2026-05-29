<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBcvRatesToQuotations extends Migration
{
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            // price_mode: 'usd' (precio lista) | 'bcv' (precio ajustado a tasa BCV)
            $table->string('price_mode', 10)->default('usd')->after('currency');
            // Snapshot of the rates used when the BCV formula was applied
            $table->decimal('binance_rate', 12, 4)->nullable()->after('price_mode');
            $table->decimal('bcv_rate', 12, 4)->nullable()->after('binance_rate');
        });
    }

    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['price_mode', 'binance_rate', 'bcv_rate']);
        });
    }
}
