<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compra', function (Blueprint $table) {
            $table->bigIncrements('id_compra');
            $table->unsignedBigInteger('id_proveedor');
            $table->dateTime('fecha_compra', 3)->default(DB::raw('CURRENT_TIMESTAMP(3)'));
            $table->decimal('total_compra', 10, 2);
            $table->decimal('descuento_compra', 10, 2)->default(0.0);
            $table->boolean('factura_compra')->default(false);
            $table->timestamps();

            // Llave foránea
            $table->foreign('id_proveedor')
                ->references('id_proveedor')
                ->on('proveedor')
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
        Schema::dropIfExists('compra');
    }
}
