<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materia';
    protected $primaryKey = 'id_materia';
    use HasFactory;

    protected $fillable = [
        'nombre_materia',
        'codigo',
    ];

    public function impartes()
    {
        return $this->hasMany(Imparte::class, 'id_materia');
    }
}
