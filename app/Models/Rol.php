<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'id_rol';
    use HasFactory;

    protected $fillable = [
        'nombre',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_rol');
    }
}
