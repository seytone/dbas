<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('quotation_number', 20)->unique();
            $table->date('emission_date');
            $table->date('expiration_date');
            $table->string('currency', 10)->default('USD');
            $table->decimal('iva_rate', 5, 2)->default(16.00);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_1', 5, 2)->default(0);
            $table->decimal('discount_1_amount', 12, 2)->default(0);
            $table->decimal('discount_2', 5, 2)->default(0);
            $table->decimal('discount_2_amount', 12, 2)->default(0);
            $table->decimal('freight', 12, 2)->default(0);
            $table->decimal('tax_exempt', 12, 2)->default(0);
            $table->decimal('tax_base', 12, 2)->default(0);
            $table->decimal('iva_amount', 12, 2)->default(0);
            $table->decimal('igtf_rate', 5, 2)->default(0);
            $table->decimal('igtf_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}
