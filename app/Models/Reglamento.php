<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reglamento extends Model
{
    protected $table = 'reglamento';
    protected $primaryKey = 'id_reglas';
    use HasFactory;
    protected $fillable = [
        'id_usuario',
        'fecha_inicio',
        'fecha_final',
        'atencion_posterior',
        'atencion_inicio',
        'atencion_final',
        'reservas_auditorio',
        'mas_reglas'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
}
