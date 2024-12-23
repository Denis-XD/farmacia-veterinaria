<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaPeriodo extends Model
{
    protected $table = 'reserva_periodo';
    protected $primaryKey = 'id_reserva_periodo';
    use HasFactory;

    protected $fillable = [
        'id_reserva',
        'id_periodo',
    ];
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva');
    }
    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'id_periodo');
    }
}
