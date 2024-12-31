<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorialInventarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historial_inventario', function (Blueprint $table) {
            $table->bigIncrements('id_historial');
            $table->unsignedBigInteger('id_producto');
            $table->integer('stock'); // Stock del producto en el momento del cambio
            $table->dateTime('fecha', 3); // Fecha del cambio
            $table->string('motivo', 255)->nullable(); // Motivo del cambio (opcional: venta, compra, ajuste)
            $table->unsignedBigInteger('id_transaccion')->nullable(); // ID de la transacción asociada
            $table->string('tipo_transaccion', 50)->nullable(); // Tipo de transacción (Compra, Venta, Ajuste)
            $table->timestamps();

            // Llave foránea hacia la tabla producto
            $table->foreign('id_producto')
                ->references('id_producto')
                ->on('producto')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historial_inventario');
    }
}
