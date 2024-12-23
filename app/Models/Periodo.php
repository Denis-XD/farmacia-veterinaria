<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $table = 'periodo';
    protected $primaryKey = 'id_periodo';
    use HasFactory;
    protected $fillable = [
        'inicio',
        'fin'
    ];
    // Accessor for 'inicio' attribute
    public function getInicioAttribute($value)
    {
        return date('H:i', strtotime($value));
    }

    // Accessor for 'fin' attribute
    public function getFinAttribute($value)
    {
        return date('H:i', strtotime($value));
    }
}
