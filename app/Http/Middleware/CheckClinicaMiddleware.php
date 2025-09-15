<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckClinicaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $usuario = session('usuario');

        if (!$usuario) {
            return redirect()->route('loginGET')->with('error', 'Você precisa estar logado.');
        }

        // Como sua session está sendo salva como coleção, pegue os IDs
        $clinicas = $usuario->pluck('ID_CLINICA')->toArray();

        $path = $request->path();

        if (str_starts_with($path, 'psicologia')) {
            if (!in_array(1, $clinicas)) {
                return abort(403, 'Acesso não autorizado para Psicologia.');
            }
        }

        if (str_starts_with($path, 'odontologia')) {
            if (!in_array(2, $clinicas)) {
                return abort(403, 'Acesso não autorizado para Odontologia.');
            }
        }

        return $next($request);
    }
}