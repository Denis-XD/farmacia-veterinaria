<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'carrera';
    protected $primaryKey = 'id_carrera';
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
    ];
    
    public function impartesCarreras()
    {
        return $this->hasMany(ImparteCarrera::class, 'id_carrera');
    }
}
