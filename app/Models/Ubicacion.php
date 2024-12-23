<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    protected $table = 'ubicacion';
    protected $primaryKey = 'id_ubicacion';
    use HasFactory;

    protected $fillable = [
        'nombre',
    ];

    public function ambientes()
    {
        return $this->hasMany(Ambiente::class, 'id_ubicacion');
    }
}
