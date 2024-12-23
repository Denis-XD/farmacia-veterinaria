<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use App\Rules\UniqueLowerCase;
use App\Rules\ExistsUpperCase;
use Spatie\Permission\Models\Role;

class UsersImport implements ToModel, WithValidation, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    public $rowsProcessed = 0;
    public $usersWithPasswords = [];
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowsProcessed++;

        $password = Str::random(10);
        $hashedPassword = Hash::make($password);

        $user = new User([
            'nombre' => strtoupper($row['nombre']),
            'celular_usuario' => $row['celular'],
            'email' => strtolower($row['email']),
            'password' => $hashedPassword,
            'active' => strtoupper($row['habilitado']) === 'SI' ? 1 : 0
        ]);

        if (isset($row['rol'])) {
            $rolName = strtoupper($row['rol']);
            $rol = Role::where('name', $rolName)->first();
            if ($rol) {
                $user->assignRole($rol);
            }
        }

        $this->usersWithPasswords[] = ['user' => $user, 'password' => $password, 'hashedPassword' => $hashedPassword];

        return $user;
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'max:40',
                'min:3',
                'regex:/^[a-zA-Z\sÑñÁáÉéÍíÓóÚú ]+$/u',
            ],
            'celular' => [
                'nullable',
                'regex:/^[0-9]{8}$/',
            ],
            'email' => [
                'required',
                'email',
                'max:50',
                'min:5',
                new UniqueLowerCase('users', 'email', 'El email ya ha sido registrado.'),
            ],
            'habilitado' => [
                'required',
                'in:SI,si,Si,sI,NO,no,No,nO',
            ],
            'rol' => [
                'required',
                new ExistsUpperCase('roles', 'name', 'El rol seleccionado no está registrado en el sistema'),
            ]
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nombre.required' => 'El campo nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe superar los 40 caracteres.',
            'nombre.min' => 'El nombre debe tener almenos 3 caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            //'celular.max' => 'El campo celular no debe superar los 8 numeros.',
            //'apellidos.min' => 'El campo apellidos debe tener almenos 4 caracteres.',
            'celular.regex' => 'El campo celular solo puede contener 8 digitos numericos.',
            'email.required' => 'El campo email es obligatorio.',
            'email.email' => 'El campo email debe incluir un signo @',
            'email.max' => 'El email no debe superar los 50 caracteres.',
            'email.min' => 'El email debe tener almenos 5 caracteres.',
            'habilitado.in' => 'El campo habilitado solo permite "SI" o "NO".',
            'rol.required' => 'El campo rol es obligatorio.',
            'rol.exists' => 'El rol seleccionado no está registrado en el sistema.',
        ];
    }
}
