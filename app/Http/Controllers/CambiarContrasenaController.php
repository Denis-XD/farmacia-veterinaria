<?php

namespace App\Http\Controllers;

use App\Mail\ChangePwdMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CambiarContrasenaController extends Controller
{
    public function index()
    {
        return view('auth.cambiar-contrasena');
    }

    public function update(Request $request)
    {
        $messages = require_once app_path('config/validation.php');
        $customAttributes = require_once app_path('config/customAttributes.php');
        $rules = [
            'current_password' => 'required|string|max:15',
            'new_password' => 'required|string|max:15|min:10',
            'rep_password' => 'required|string|max:15|min:10|same:new_password',
        ];

        $validatedData = $request->validate($rules, $messages, $customAttributes);

        if (!Hash::check($validatedData['current_password'], Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.'])->withInput();
        }

        try {
            $user_id = Auth::user()->id;
            $user = User::find($user_id);
            $user->password = Hash::make($validatedData['new_password']);
            DB::beginTransaction();
            $user->save();
            $user['password'] = $validatedData['new_password'];

            Mail::to($user->email)->send(new ChangePwdMail($user));
            //dd($response);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cambiar_contrasena.index')->with('error', 'Ocurrió un error al intentar actualizar la contraseña.');
        }

        return redirect()->route('cambiar_contrasena.index')->with('success', 'Contraseña actualizada correctamente.');
    }
}
