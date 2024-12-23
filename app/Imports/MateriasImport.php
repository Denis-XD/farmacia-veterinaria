<?php

namespace App\Imports;

use App\Models\Materia;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use App\Rules\UniqueUpperCase;

class MateriasImport implements ToModel, WithValidation, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    public $rowsProcessed = 0;
    /**
     * Método que crea una instancia del modelo Materia por cada fila.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowsProcessed++;
        return new Materia([
            'codigo' => $row['codigo'],
            'nombre_materia' => strtoupper($row['nombre_materia']),
        ]);
    }
    /**
     * Reglas de validación para la importación.
     *
     * @return array
     */

    public function rules(): array
    {
        return [
            'codigo' => [
                'required',
                'bail',
                'numeric',
                'digits_between:1,10',
                'unique:materia,codigo',
            ],
            'nombre_materia' => [
                'required',
                'max:40',
                'min:4',
                'regex:/^[a-zA-Z0-9\sÑñÁáÉéÍíÓóÚú.]+$/u',
                new UniqueUpperCase('materia', 'nombre_materia', 'El nombre de la materia ya ha sido registrado.')
            ]
        ];
    }
    /**
     * Mensajes de validación personalizados.
     *
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'codigo.required' => 'El campo código es obligatorio.',
            'codigo.numeric' => 'El campo código debe ser numérico.',
            'codigo.digits_between' => 'El campo código debe tener entre 1 y 10 dígitos.',
            'codigo.unique' => 'El código ingresado ya ha sido registrado.',
            'nombre_materia.required' => 'El nombre de la materia es obligatorio.',
            'nombre_materia.regex' => 'El nombre de la materia solo puede contener letras, números, espacios y el signo de punto.',
            'nombre_materia.max' => 'El nombre de la materia no debe superar los 40 caracteres.',
            'nombre_materia.min' => 'El nombre de la materia debe tener al menos 4 caracteres.',
            'nombre_materia.unique' => 'El nombre de la materia ya ha sido registrado.'
        ];
    }
}
