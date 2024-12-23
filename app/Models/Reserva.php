<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reserva';
    protected $primaryKey = 'id_reserva';

    protected $fillable = [
        'fecha_solicitud',
        'fecha_reserva',
        'descripcion',
        'id_estado',
        'generico',
        'grupal',
        'capacidad_solicitada',
        'capacidad_total',
        'id_tipo'
    ];

    public function reservasPeriodos()
    {
        return $this->hasMany(ReservaPeriodo::class, 'id_reserva');
    }

    public function usuarios()
    {
        return $this->hasMany(ReservaUsuario::class, 'id_reserva')->with('usuario');
    }    
    
    public function ambientes()
    {
        return $this->belongsToMany(Ambiente::class, 'reserva_ambiente', 'id_reserva', 'id_ambiente');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }

    // Nueva relación para tipo de ambiente
    public function tipoAmbiente()
    {
        return $this->belongsTo(TipoAmbiente::class, 'id_tipo');
    }

    // Nueva relación para reservasImparte
    public function reservasImparte()
    {
        return $this->hasManyThrough(
            ReservaUsuarioImparte::class,
            ReservaUsuario::class,
            'id_reserva', // Foreign key on ReservaUsuario table...
            'id_reserva_usuario', // Foreign key on ReservaUsuarioImparte table...
            'id_reserva', // Local key on Reserva table...
            'id_reserva_usuario' // Local key on ReservaUsuario table...
        );
    }
}
