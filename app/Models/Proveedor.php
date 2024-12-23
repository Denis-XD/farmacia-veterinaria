<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedor';

    protected $primaryKey = 'id_proveedor';

    protected $fillable = [
        'nombre_proveedor',
        'direccion',
        'celular_proveedor',
    ];

    // Relación con Compra
    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_proveedor', 'id_proveedor');
    }
}
