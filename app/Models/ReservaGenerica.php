<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaGenerica extends Model
{
    use HasFactory;

    protected $table = 'reserva_generica';
    protected $primaryKey = 'id_reserva_generica';

    protected $fillable = [
        'id_reserva',
        'id_tipo',
        'capacidad',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva');
    }

    public function tipoAmbiente()
    {
        return $this->belongsTo(TipoAmbiente::class, 'id_tipo');
    }
}
