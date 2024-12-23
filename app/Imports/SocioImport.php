<?php

namespace App\Imports;

use App\Models\Socio;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use App\Rules\UniqueUpperCase;

class SocioImport implements ToModel, WithValidation, WithHeadingRow, SkipsEmptyRows
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
        return new Socio([
            'nombre_socio' => strtoupper($row['nombre']),
            'celular_socio' => $row['celular'],
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
            'nombre' => [
                'required',
                'max:50',
                'min:4',
                'regex:/^[a-zA-Z\sÑñÁáÉéÍíÓóÚú ]+$/u',
                new UniqueUpperCase('socio', 'nombre_socio', 'El nombre del socio ya ha sido registrado.')
            ],
            'celular' => [
                'nullable',
                'regex:/^[0-9]{8}$/',
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
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El nombre del socio no debe superar los 50 caracteres.',
            'nombre.min' => 'El nombre del socio debe tener al menos 4 caracteres.',
            'nombre.regex' => 'El nombre del socio solo puede contener letras y espacios.',
            'celular.regex' => 'El campo celular solo puede contener 8 digitos numericos.',
        ];
    }
}
