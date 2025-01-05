<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->bigIncrements('id_producto');
            $table->string('codigo_barra', 191)->nullable()->unique();
            $table->string('nombre_producto', 191)->unique();
            $table->string('unidad', 191);
            $table->dateTime('fecha_vencimiento', 3)->nullable();
            $table->double('porcentaje_utilidad', 8, 2)->default(20.0);
            $table->double('precio_compra_actual', 8, 2);
            $table->double('precio_venta_actual', 8, 2);
            $table->integer('stock');
            $table->integer('stock_minimo')->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producto');
    }
}
