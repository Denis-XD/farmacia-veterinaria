<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pago', function (Blueprint $table) {
            $table->bigIncrements('id_pago'); 
            $table->unsignedBigInteger('id_venta'); 
            $table->dateTime('fecha_pago'); 
            $table->decimal('monto_pagado', 10, 2); 
            $table->decimal('saldo_pendiente', 10, 2)->nullable(); 
            $table->timestamps(); 

            // Llave foránea
            $table->foreign('id_venta')
                  ->references('id_venta')
                  ->on('venta')
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
        Schema::dropIfExists('pago');
    }
}
