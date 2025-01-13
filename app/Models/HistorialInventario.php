<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialInventario extends Model
{
    use HasFactory;

    protected $table = 'historial_inventario';
    protected $primaryKey = 'id_historial';

    protected $fillable = [
        'id_producto',
        'stock',               // Cantidad de stock afectada
        'fecha',               // Fecha del cambio
        'motivo',              // Motivo del cambio (Compra, Venta, Ajuste, etc.)
        'id_transaccion',      // ID de la transacción asociada
        'tipo_transaccion',    // Tipo de transacción (Compra, Venta, Ajuste)
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    // Relación con el modelo Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    // Relación polimórfica opcional para transacciones
    public function transaccion()
    {
        if ($this->tipo_transaccion === 'Compra') {
            return $this->belongsTo(Compra::class, 'id_transaccion', 'id_compra');
        } elseif ($this->tipo_transaccion === 'Venta') {
            return $this->belongsTo(Venta::class, 'id_transaccion', 'id_venta');
        }
        return null;
    }
}
