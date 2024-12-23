<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImparteCarrera extends Model
{
    protected $table = 'imparte_carrera';
    protected $primaryKey = 'id_imparte_carrera';
    use HasFactory;

    protected $fillable = [
        'id_imparte',
        'id_carrera',
        'nivel',
    ];

    public function imparte()
    {
        return $this->belongsTo(Imparte::class, 'id_imparte');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }
}
