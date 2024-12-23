<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaAmbiente extends Model
{
    use HasFactory;

    protected $table = 'reserva_ambiente';
    protected $fillable = [
        'id_reserva',
        'id_ambiente',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva');
    }

    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class, 'id_ambiente');
    }
}
