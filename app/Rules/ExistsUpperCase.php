<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExistsUpperCase implements Rule
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
        $value = strtoupper($value);
        return DB::table($this->table)->whereRaw("UPPER($this->column) = ?", [$value])->exists();
    }

    public function message()
    {
        return $this->message ?: 'El campo seleccionado no es válido.';
    }
}
