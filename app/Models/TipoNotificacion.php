<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoNotificacion extends Model
{
    protected $table = 'tipo_notificacion';
    protected $primaryKey = 'id_tipo_notificacion';
    use HasFactory;

    protected $fillable = [
        'nombre',
    ];

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_tipo_notificacion');
    }
}
