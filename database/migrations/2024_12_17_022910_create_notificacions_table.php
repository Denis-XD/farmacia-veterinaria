<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificacion', function (Blueprint $table) {
            $table->bigIncrements('id_notificacion');
            $table->string('asunto', 100);
            $table->text('contenido');
            $table->unsignedBigInteger('id_tipo_notificacion');
            $table->foreign('id_tipo_notificacion')->references('id_tipo_notificacion')->on('tipo_notificacion')->onDelete('cascade');
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
        Schema::dropIfExists('notificacion');
    }
}