<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imparte extends Model
{
    protected $table = 'imparte';
    protected $primaryKey = 'id_imparte';
    use HasFactory;

    protected $fillable = [
        'id_docente',
        'id_materia',
        'id_grupo',
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'id_docente');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo');
    }

    public function impartesCarreras()
    {
        return $this->hasMany(ImparteCarrera::class, 'id_imparte');
    }
}
