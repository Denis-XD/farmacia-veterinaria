<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'venta';
    protected $primaryKey = 'id_venta';

    protected $fillable = [
        'id_usuario',
        'id_socio',
        'fecha_venta',
        'total_venta',
        'descuento_venta',
        'credito',
        'servicio',
        'finalizada',
        'descripcion',
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
    ];

    public function socio()
    {
        return $this->belongsTo(Socio::class, 'id_socio', 'id_socio');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_venta', 'id_venta');
    }

    public function servicioVeterinario()
    {
        return $this->hasOne(Servicio::class, 'id_venta', 'id_venta');
    }
}
