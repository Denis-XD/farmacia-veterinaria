<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venta', function (Blueprint $table) {
            $table->bigIncrements('id_venta');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_socio')->nullable();
            $table->dateTime('fecha_venta', 3)->default(DB::raw('CURRENT_TIMESTAMP(3)'));
            $table->decimal('total_venta', 10, 2);
            $table->decimal('descuento_venta', 10, 2)->default(0.0);
            $table->boolean('credito')->default(false);
            $table->boolean('servicio')->default(false);
            $table->boolean('finalizada')->default(false);
            $table->string('descripcion', 200)->nullable();
            $table->timestamps();

            // Llaves foráneas
            $table->foreign('id_usuario')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('id_socio')
                ->references('id_socio')
                ->on('socio')
                ->onDelete('set null')
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
        Schema::dropIfExists('venta');
    }
}
