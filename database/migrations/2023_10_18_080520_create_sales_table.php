<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
			$table->bigInteger('client_id')->unsigned();
			$table->bigInteger('seller_id')->unsigned();
			$table->enum('invoice_type', ['nota', 'factura'])->default('nota');
			$table->string('invoice_number');
			$table->enum('payment_method', ['efectivo', 'deposito', 'dolares', 'bolivares', 'zelle', 'paypal', 'binance', 'panama'])->default('efectivo');
			$table->enum('payment_currency', ['usd', 'mix'])->default('usd')->comment('Moneda de pago = usd | mix');
			$table->double('payment_amount_usd')->default(0)->comment('Pago en dólares');
			$table->double('payment_amount_bsf')->default(0)->comment('Pago en bolívares');
			$table->double('subtotal')->default(0)->comment('Subtotal = SUM(productos)');
			$table->double('iva')->default(0)->comment('Impuestos = subtotal * 16% (aplica siempre)');
			$table->double('igtf')->default(0)->comment('IGTF = subtotal * 3% (aplica solo cuando pago en dólares)');
			$table->double('cityhall')->default(0)->comment('Alcaldía = subtotal * 9% (aplica solo cuando se entrega factura)');
			$table->double('total')->default(0)->comment('Total (Base imponible) = subtotal - iva');
			$table->double('provider')->default(0)->comment('Costo proveedor');
			$table->double('profit')->default(0)->comment('Ganancia = total - provider');
			$table->double('commission')->default(0)->comment('Comisión vendedor = profit * (% comisión)');
			$table->double('commission_perpetual')->default(0)->comment('Comisión licencias perpetuas');
			$table->double('commission_annual')->default(0)->comment('Comisión suscripciones anuales');
			$table->double('commission_hardware')->default(0)->comment('Comisión hardware y otros');
			$table->double('commission_services')->default(0)->comment('Comisión servicios');
			$table->text('trello')->nullable()->default(null);
			$table->text('notes')->nullable()->default(null);
			$table->timestamp('registered_at');
			$table->softDeletes();
            $table->timestamps();

			$table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');
			$table->foreign('seller_id')->references('id')->on('sellers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
