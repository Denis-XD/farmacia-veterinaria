<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ambiente extends Model
{
    use HasFactory;
    
    protected $table = 'ambiente';
    protected $primaryKey = 'id_ambiente';

    protected $fillable = [
        'nombre',
        'capacidad',
        'descripcion',
        'habilitado',
        'id_ubicacion',
        'id_tipo'
    ];

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'id_ubicacion');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoAmbiente::class, 'id_tipo');
    }

    public function reservas()
    {
        return $this->belongsToMany(Reserva::class, 'reserva_ambiente', 'id_ambiente', 'id_reserva');
    }
}
