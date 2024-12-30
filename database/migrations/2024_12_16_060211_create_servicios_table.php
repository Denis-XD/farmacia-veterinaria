<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicio', function (Blueprint $table) {
            $table->bigIncrements('id_servicio');
            $table->unsignedBigInteger('id_venta');
            $table->string('tratamiento', 200);
            $table->dateTime('fecha_servicio');
            $table->double('costo_servicio', 10, 2);
            $table->double('costo_combustible', 10, 2);
            $table->double('total_servicio', 10, 2);
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
        Schema::dropIfExists('servicio');
    }
}
