<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueUpperCase implements Rule
{
    protected $table;
    protected $column;
    protected $message;

    public function __construct($table, $column, $message = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->message = $message;
    }

    public function passes($attribute, $value)
    {
        $value = strtoupper($value); // Convertir el valor a mayúsculas antes de verificar
        return DB::table($this->table)->whereRaw("UPPER($this->column) = ?", [$value])->doesntExist();
    }

    public function message()
    {
        // Usar el mensaje personalizado si está definido, de lo contrario usar un mensaje predeterminado
        return $this->message ?? "El :attribute ya ha sido registrado.";
    }
}
