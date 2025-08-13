<?php

namespace App\Http\Controllers\Psicologia;

use App\Http\Controllers\Controller;
use App\Models\FaesaClinicaPsicologo;
use Illuminate\Http\Request;

class PsicologoController extends Controller
{
    public function getPsicologos()
    {
        return FaesaClinicaPsicologo::all();
    }
}
