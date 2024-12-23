<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_compra', function (Blueprint $table) {
            $table->bigIncrements('id_detalle_compra'); 
            $table->unsignedBigInteger('id_compra'); 
            $table->unsignedBigInteger('id_producto'); 
            $table->string('descripcion', 191)->nullable(); 
            $table->integer('cantidad_compra'); 
            $table->double('subtotal_compra', 10, 2); 
            $table->timestamps(); 

            // Llaves foráneas
            $table->foreign('id_compra')
                  ->references('id_compra')
                  ->on('compra')
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
        Schema::dropIfExists('detalle_compra');
    }
}
