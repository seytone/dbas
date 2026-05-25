<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterQuotationProductsDescriptionToLongtext extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE quotation_products MODIFY description LONGTEXT NOT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE quotation_products MODIFY description TEXT NOT NULL');
    }
}
