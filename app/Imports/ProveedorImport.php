<?php

namespace App\Imports;

use App\Models\Proveedor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use App\Rules\UniqueUpperCase;

class ProveedorImport implements ToModel, WithValidation, WithHeadingRow, SkipsEmptyRows
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
        return new Proveedor([
            'nombre_proveedor' => strtoupper($row['nombre']),
            'direccion' => strtoupper($row['direccion']),
            'celular_proveedor' => $row['celular'],
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
                new UniqueUpperCase('proveedor', 'nombre_proveedor', 'El nombre del proveedor ya ha sido registrado.')
            ],
            'direccion' => [
                'nullable',
                'max:50',
                'min:0',
                'regex:/^[a-zA-Z0-9\sÑñÁáÉéÍíÓóÚú. ]+$/u',
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
            'nombre.max' => 'El nombre del proveedor no debe superar los 50 caracteres.',
            'nombre.min' => 'El nombre del proveedor debe tener al menos 4 caracteres.',
            'nombre.regex' => 'El nombre del proveedor solo puede contener letras y espacios.',
            'direccion.max' => 'La direccion no debe superar los 50 caracteres.',
            'direccion.regex' => 'El nombre del proveedor solo puede contener letras y espacios ',
            'celular.regex' => 'El campo celular solo puede contener 8 digitos numericos.',
        ];
    }
}
