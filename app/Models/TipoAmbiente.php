<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAmbiente extends Model
{
    protected $table = 'tipo_ambiente';
    protected $primaryKey = 'id_tipo';
    use HasFactory;

    protected $fillable = [
        'nombre',
        'color',
    ];

    public function ambientes()
    {
        return $this->hasMany(Ambiente::class, 'id_tipo');
    }
}
