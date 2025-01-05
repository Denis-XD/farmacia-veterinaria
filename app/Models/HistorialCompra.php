<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialCompra extends Model
{
    use HasFactory;

    protected $table = 'historial_compra';
    protected $primaryKey = 'id_historial';

    protected $fillable = [
        'id_producto',
        'precio_compra',
        'fecha_inicio',
        'fecha_fin',
    ];

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
