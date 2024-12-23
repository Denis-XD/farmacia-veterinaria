<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaUsuarioImparte extends Model
{
    use HasFactory;

    protected $table = 'reserva_usuario_imparte';
    protected $primaryKey = 'id_reserva_usuario_imparte';
    protected $fillable = ['id_reserva_usuario', 'id_imparte'];

    public function reservaUsuario()
    {
        return $this->belongsTo(ReservaUsuario::class, 'id_reserva_usuario');
    }

    public function imparte()
    {
        return $this->belongsTo(Imparte::class, 'id_imparte');
    }
}
