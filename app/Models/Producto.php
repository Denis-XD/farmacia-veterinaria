<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'id_producto';
    use HasFactory;

    protected $fillable = [
        'codigo_barra',
        'nombre_producto',
        'unidad',
        'fecha_vencimiento',
        'porcentaje_utilidad',
        'precio_compra_actual',
        'precio_venta_actual',
        'stock',
        'stock_minimo',
    ];

    public function historialPrecios()
    {
        return $this->hasMany(HistorialPrecio::class, 'id_producto', 'id_producto');
    }

    // Obtener el precio actual del producto
    public function precioActual()
    {
        return $this->hasOne(HistorialPrecio::class, 'id_producto', 'id_producto')
            ->whereNull('fecha_fin');
    }

    // Relación con DetalleCompra
    public function detallesCompra()
    {
        return $this->hasMany(DetalleCompra::class, 'id_producto', 'id_producto');
    }

    // Relación con historial de inventario
    public function historialInventario()
    {
        return $this->hasMany(HistorialInventario::class, 'id_producto', 'id_producto');
    }

    public function historialPreciosCompra()
    {
        return $this->hasMany(HistorialCompra::class, 'id_producto', 'id_producto');
    }

    // Obtener el precio actual del producto
    public function precioCompraActual()
    {
        return $this->hasOne(HistorialCompra::class, 'id_producto', 'id_producto')
            ->whereNull('fecha_fin');
    }
}
