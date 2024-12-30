<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Socio extends Model
{
    protected $table = 'socio';
    protected $primaryKey = 'id_socio';
    use HasFactory;

    protected $fillable = [
        'nombre_socio',
        'celular_socio',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_socio', 'id_socio');
    }
}
