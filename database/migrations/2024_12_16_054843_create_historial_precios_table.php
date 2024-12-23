<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorialPreciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historial_precio', function (Blueprint $table) {
            $table->bigIncrements('id_historial'); 
            $table->unsignedBigInteger('id_producto'); 
            $table->double('precio_venta', 8, 2); 
            $table->dateTime('fecha_inicio', 3); 
            $table->dateTime('fecha_fin', 3)->nullable(); 
            $table->timestamps(); 
            
            // Llave foránea
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
        Schema::dropIfExists('historial_precio');
    }
}
