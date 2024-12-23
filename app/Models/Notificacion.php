<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificacion';
    protected $primaryKey = 'id_notificacion';

    use HasFactory;

    protected $fillable = [
        'asunto',
        'contenido',
        'id_tipo_notificacion'
    ];

    public function tipo()
    {
        return $this->belongsTo(TipoNotificacion::class, 'id_tipo_notificacion');
    }

    public function destinatarios()
    {
        return $this->hasMany(Destinatario::class, 'id_notificacion');
    }
}
