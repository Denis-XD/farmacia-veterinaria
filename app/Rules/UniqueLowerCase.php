<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueLowerCase implements Rule
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
        $value = strtolower($value);
        return DB::table($this->table)->whereRaw("LOWER($this->column) = ?", [$value])->doesntExist();
    }

    public function message()
    {
        return $this->message ?? "El :attribute ya ha sido registrado.";
    }
}
