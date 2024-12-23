<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Mail\UserCreatedMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('usuario_listar'), 403);

        $buscar = $request->get('buscar');

        $usuarios = User::orderBy('id', 'asc')
            ->where(function ($query) use ($buscar) {
                // Dividimos la cadena de búsqueda en palabras individuales
                $terminosBusqueda = explode(' ', $buscar);

                foreach ($terminosBusqueda as $termino) {
                    // Buscamos en el campo de nombre y apellido
                    $query->where('nombre', 'like', '%' . $termino . '%')
                        ->orWhere('celular_usuario', 'like', '%' . $termino . '%')
                        ->orWhere('email', 'like', '%' . $termino . '%');
                }
            })
            ->paginate(10)->appends($request->query());

        $roles = Role::all()->pluck('name', 'id');
        
        return view('pages.usuarios', compact('usuarios', 'roles', 'buscar'));
    }

    public function buscar(Request $request)
    {
        $query = $request->get('query'); // Obtener el término de búsqueda del formulario
        echo $request;
        // Realizar la búsqueda en la base de datos
        $usuarios = User::where('nombre', 'like', "%$query%")
            ->orWhere('celular_usuario', 'like', "%$query%")
            ->orWhere('email', 'like', "%$query%")
            ->paginate(10);

        // Devolver los resultados de la búsqueda a la vista
        return view('pages.usuarios', compact('usuarios'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('usuario_crear'), 403);
        try {
            DB::beginTransaction();

            $request->merge(['active' => $request->has('active') ? 1 : 0]);
            $messages = require_once app_path('config/validation.php');
            $customAttributes = require_once app_path('config/customAttributes.php');
            $rules = [
                'nombre' => 'required|string|max:40|min:3',
                'celular_usuario' => 'max:8|min:0',
                'active' => 'required|boolean',
                'email' => 'required|email|max:50|min:5|unique:users,email',
            ];

            $password = Str::random(10);
            $hashedPassword = Hash::make($password);
            $validatedData = $request->validate($rules, $messages, $customAttributes);
            $validatedData['password'] = $hashedPassword;
            $user = User::create($validatedData);
            $user['password'] = $password; // Send password to email
            $roles = $request->input('roles', []);
            $user->syncRoles($roles);
            Mail::to($request->email)->send(new UserCreatedMail($user));

            DB::commit();

            return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('usuarios.index')->with('error', 'Error al crear el usuario.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('usuario_actualizar'), 403);
        $request->merge(['active' => $request->has('active') ? 1 : 0]);
        $messages = require_once app_path('config/validation.php');
        $customAttributes = require_once app_path('config/customAttributes.php');
        $rules = [
            'nombre' => 'required|string|max:40|min:3',
            'celular_usuario' => 'max:8|min:0',
            'active' => 'required|boolean',
            'email' => 'required|email|max:50|min:5|unique:users,email,' . $id,
        ];

        $validatedData = $request->validate($rules, $messages, $customAttributes);
        $usuario = User::findOrFail($id);

        if ($usuario->email != $validatedData['email']) {
            $newPassword = Str::random(10);
            $hashedPassword = Hash::make($newPassword);

            $validatedData['password'] = $hashedPassword;
            $usuario['password'] = $newPassword;
            $usuario['email'] = $validatedData['email'];
            Mail::to($validatedData['email'])->send(new UserCreatedMail($usuario, $newPassword));
        }

        $usuario->update($validatedData);
        $roles = $request->input('roles', []);
        $usuario->syncRoles($roles);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('usuario_eliminar'), 403);
        try {
            $usuario = User::findOrFail($id);
            $usuario->delete();
            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al eliminar el usuario.');
        }
    }

    public function login(Request $request)
    {
        /*$credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/');
        }
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);*/
        $email = strtolower($request->input('email')); // Convert email to uppercas
        $password = $request->input('password');

        $credentials = compact('email', 'password'); // Create credentials array

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        $import = new UsersImport;

        try {
            Excel::import($import, $request->file('file'));

            if ($import->rowsProcessed === 0) {
                return back()->with('error', 'No se encontraron registros en el archivo o el archivo está vacío.');
            }

            foreach ($import->usersWithPasswords as $userWithPassword) {
                $user = $userWithPassword['user'];
                $password = $userWithPassword['password'];
                $hashedPassword = $userWithPassword['hashedPassword'];

                $user['password'] = $password;
                Mail::to($user->email)->send(new UserCreatedMail($user, $password));
                $user['password'] = $hashedPassword;
            }
        } catch (ValidationException $e) {
            $failures = $e->failures();

            $erroresPorFila = [];

            foreach ($failures as $failure) {
                $fila = $failure->row();
                if (!isset($erroresPorFila[$fila])) {
                    $erroresPorFila[$fila] = ["Hubo un error en la fila {$fila}:"];
                }

                foreach ($failure->errors() as $error) {
                    $erroresPorFila[$fila][] = $error;
                }
            }

            $erroresTraducidos = [];

            foreach ($erroresPorFila as $fila => $errores) {
                $erroresTraducidos[] = implode("\n", $errores);
            }

            $erroresFinales = nl2br(implode("\n", $erroresTraducidos));

            return back()->with('error', $erroresFinales);
        }
        return back()->with('success', 'Usuarios importados correctamente.');
    }
}
