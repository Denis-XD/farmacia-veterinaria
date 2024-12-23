<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaUsuario extends Model
{
    use HasFactory;

    protected $table = 'reserva_usuario';
    protected $primaryKey = 'id_reserva_usuario';
    protected $fillable = ['id_reserva', 'id_usuario'];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function reservasImparte()
    {
        return $this->hasMany(ReservaUsuarioImparte::class, 'id_reserva_usuario');
    }
}
