<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destinatario extends Model
{
    protected $table = 'destinatario';
    protected $primaryKey = 'id_destinatario';

    use HasFactory;

    protected $fillable = [
        'id_notificacion',
        'id_usuario'
    ];

    public function notificacion()
    {
        return $this->belongsTo(Notificacion::class, 'id_notificacion');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
