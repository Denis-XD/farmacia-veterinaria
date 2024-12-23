<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensaje';
    protected $primaryKey = 'id_mensaje';

    use HasFactory;

    protected $fillable = [
        'asunto',
        'contenido',
        'id_estado_mensaje',
        'id_usuario',
    ];

    public function estado()
    {
        return $this->belongsTo(EstadoMensaje::class, 'id_estado_mensaje');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
