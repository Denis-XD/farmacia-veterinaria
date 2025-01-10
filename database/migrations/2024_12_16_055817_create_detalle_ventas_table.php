<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_venta', function (Blueprint $table) {
            $table->bigIncrements('id_detalle_venta');
            $table->unsignedBigInteger('id_venta');
            $table->unsignedBigInteger('id_producto');
            $table->decimal('cantidad_venta');
            $table->decimal('subtotal_venta', 10, 2);
            $table->timestamps();

            // Llaves foráneas
            $table->foreign('id_venta')
                ->references('id_venta')
                ->on('venta')
                ->onDelete('cascade')
                ->onUpdate('cascade');

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
        Schema::dropIfExists('detalle_venta');
    }
}
