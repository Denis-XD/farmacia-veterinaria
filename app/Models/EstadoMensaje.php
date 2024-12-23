<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoMensaje extends Model
{
    protected $table = 'estado_mensaje';
    protected $primaryKey = 'id_estado_mensaje';
    use HasFactory;
    protected $fillable = [
        'nombre',
    ];
}
