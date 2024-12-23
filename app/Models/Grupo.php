<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupo';
    protected $primaryKey = 'id_grupo';
    use HasFactory;

    protected $fillable = [
        'nombre',
    ];

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_grupo');
    }
}
