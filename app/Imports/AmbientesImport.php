<?php

namespace App\Imports;

use App\Models\Ambiente;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\Importable;
use App\Rules\ExistsUpperCase;
use App\Rules\UniqueUpperCase;
use App\Models\TipoAmbiente;
use App\Models\Ubicacion;

class AmbientesImport implements ToModel, WithValidation, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    public $rowsProcessed = 0;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowsProcessed++;

        $tipoAmbiente = TipoAmbiente::where('nombre', strtoupper($row['tipo']))->first();
        $ubicacion = Ubicacion::where('nombre', strtoupper($row['ubicacion']))->first();

        $ambiente = new Ambiente([
            'nombre' => strtoupper($row['nombre']),
            'capacidad' => $row['capacidad'],
            'descripcion' => strtoupper($row['descripcion']),
            'habilitado' => strtoupper($row['habilitado']) === 'SI' ? 1 : 0,
            'id_tipo' => $tipoAmbiente->id_tipo,
            'id_ubicacion' => $ubicacion->id_ubicacion,
        ]);

        return $ambiente;
    }

    public function rules(): array
    {
        return [
            'tipo' => [
                'required',
                new ExistsUpperCase('tipo_ambiente', 'nombre', 'El tipo de ambiente seleccionado no está registrado en el sistema'),
            ],
            'ubicacion' => [
                'required',
                new ExistsUpperCase('ubicacion', 'nombre', 'La ubicacion seleccionada no está registrada en el sistema'),
            ],
            'nombre' => [
                'required',
                'max:40',
                'min:4',
                'regex:/^[a-zA-Z0-9\sÑñÁáÉéÍíÓóÚú]+$/u',
                new UniqueUpperCase('ambiente', 'nombre', 'El nombre ya ha sido registrado.'),
            ],
            'capacidad' => [
                'required',
                'bail',
                'numeric',
                'min:100',
                'max:300',
            ],
            'habilitado' => [
                'required',
                'in:SI,si,Si,sI,NO,no,No,nO',
            ],
            'descripcion' => [
                'nullable',
                'max:200',
                'regex:/^[a-zA-Z0-9\s\nÑñÁáÉéÍíÓóÚú.,:]*$/u',
            ]
        ];
    }

    public function customValidationMessages()
    {
        return [
            'tipo.required' => 'El campo tipo de ambiente es obligatorio.',
            'ubicacion.required' => 'El campo ubicacion es obligatorio.',
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.regex' => 'El campo nombre solo puede contener letras, números y espacios.',
            'nombre.max' => 'El nombre no debe superar los 40 caracteres.',
            'nombre.min' => 'El nombre debe tener almenos 4 caracteres.',
            'capacidad.required' => 'El campo capacidad es obligatorio.',
            'capacidad.numeric' => 'La capacidad debe ser un número.',
            'capacidad.min' => 'La capacidad no puede ser menor que 100.',
            'capacidad.max' => 'La capacidad no puede ser mayor que 300.',
            'habilitado.required' => 'El campo habilitado es obligatorio.',
            'habilitado.in' => 'El campo habilitado solo permite "SI" o "NO".',
            'descripcion.regex' => 'El campo descripcion solo puede contener letras, números, espacios y signos de ",.:".',
            'descripcion.max' => 'La descripcion no debe superar los 200 caracteres.',
        ];
    }
}
