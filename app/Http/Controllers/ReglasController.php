<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reglamento;
use App\Models\User;
use App\Mail\Reglas;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Gate;

class ReglasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('reglas'), 403);
        $ultimasReglas = Reglamento::latest()->first();
        if ($ultimasReglas) {
            if ($ultimasReglas->fecha_inicio) {
                $ultimasReglas->fecha_inicio = Carbon::parse($ultimasReglas->fecha_inicio)->toDateString();
            }

            if ($ultimasReglas->fecha_final) {
                $ultimasReglas->fecha_final = Carbon::parse($ultimasReglas->fecha_final)->toDateString();
            }
        }
        return view('pages.reglas', ['ultimasReglas' => $ultimasReglas]);
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
        //abort_if(Gate::denies('ambiente_crear'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'fecha_inicio' => 'required|date',
            'fecha_final' => 'required|date',
            'atencion_posterior' => 'required|integer|min:0|max:100',
            'atencion_inicio' => 'required',
            'atencion_final' => 'required',
            'reservas_auditorio' => 'required|integer|min:0|max:10',
            'mas_reglas' => 'max:200|min:0',
        ];
        $userId = Auth::id();
        $user = User::find($userId);
        $data = $request->all();

        // Comparar fechas y horas de atención
        $fechaInicio = Carbon::parse($data['fecha_inicio']);
        $fechaFinal = Carbon::parse($data['fecha_final']);
        $horaInicio = Carbon::parse($data['atencion_inicio']);
        $horaFinal = Carbon::parse($data['atencion_final']);
        
        $validator = Validator::make($data, $rules);
        
        // Comprobar reglas de validación predefinidas
        if ($validator->fails()) {
            $errors = $validator->errors();
        } else {
            $errors = new \Illuminate\Support\MessageBag();
        }
        if ($fechaInicio >= $fechaFinal) {
            $errors->add('fecha_inicio', 'La fecha de inicio de reservas debe ser anterior a la fecha final de reservas.');
        }
        if ($horaInicio >= $horaFinal) {
            $errors->add('atencion_inicio', 'La hora de inicio de atención debe ser menor que la hora de finalización de atención.');
        }
            
        if ($errors->isNotEmpty()) {
            return redirect()->back()->withInput()->withErrors($errors);
        }
        
        
        // Validación exitosa, guardar los datos
        $validatedData = $validator->validated();
        $validatedData['id_usuario'] = $userId;

        $usuarios = User::all();
        Reglamento::create($validatedData);

        $ultimasReglas = Reglamento::latest()->first();
        foreach ($usuarios as $usuario) {
            Mail::to($usuario->email)->send(new Reglas($usuario, $ultimasReglas));
        }
        return redirect()->route('reglas.index')->with('success', 'Reglas guardadas correctamente.');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
