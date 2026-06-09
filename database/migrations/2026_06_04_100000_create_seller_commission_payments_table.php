<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerCommissionPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('seller_commission_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id');
            $table->smallInteger('year');
            $table->tinyInteger('month');

            // Snapshot of the commission amounts at the time of payment.
            $table->decimal('total_commission', 12, 2)->default(0);
            $table->decimal('commission_perpetual', 12, 2)->default(0);
            $table->decimal('commission_annual', 12, 2)->default(0);
            $table->decimal('commission_hardware', 12, 2)->default(0);
            $table->decimal('commission_services', 12, 2)->default(0);

            // Payment tracking.
            $table->enum('payment', ['pending', 'completed'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->string('payment_evidence')->nullable();
            $table->text('comments')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->unique(['seller_id', 'year', 'month'], 'unique_seller_period');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seller_commission_payments');
    }
}
