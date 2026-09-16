<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExtendProductsDescriptionLength extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Product descriptions grew past the default VARCHAR(255); we push
            // to 500 to match the new form/validation limit.
            $table->string('description', 500)->nullable()->default(null)->change();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('description', 255)->nullable()->default(null)->change();
        });
    }
}
