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
			$table->enum('type', ['nota', 'factura'])->default('nota');
			$table->string('invoice_number');
			$table->text('notes')->nullable()->default(null);
			$table->enum('payment_method', ['efectivo', 'deposito', 'dolares', 'bolivares', 'zelle', 'paypal', 'binance', 'panama'])->default('efectivo');
			$table->double('subtotal')->default(0)->comment('Subtotal = SUM(productos)');
			$table->double('iva')->default(0)->comment('Impuestos = subtotal * 16% (aplica siempre)');
			$table->double('igtf')->default(0)->comment('IGTF = subtotal * 3% (aplica solo cuando pago en dólares)');
			$table->double('cityhall')->default(0)->comment('Alcaldía = subtotal * 9% (aplica solo cuando se entrega factura)');
			$table->double('total')->default(0)->comment('Total (Base imponible) = subtotal - iva');
			$table->double('provider')->default(0)->comment('Costo proveedor = total * 30%');
			$table->double('profit')->default(0)->comment('Ganancia = total - provider');
			$table->double('commission')->default(0)->comment('Comisión vendedor = profit * (% comisión)');
			$table->text('trello')->nullable()->default(null);
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
