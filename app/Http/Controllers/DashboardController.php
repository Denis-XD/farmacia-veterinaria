<?php

namespace App\Http\Controllers;

use App\Models\Ambiente;
use App\Models\Carrera;
use App\Models\Estado;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Reserva;
use App\Models\TipoAmbiente;
use App\Models\Ubicacion;
use App\Models\User;
use App\Models\Reglamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
    

        return view('pages.preubas');
    }
}
