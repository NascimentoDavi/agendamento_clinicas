<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // ARMAZENA LOGIN DE USUARIO E ID DA CLINICA
        $usuario = session('usuario');

        if ($usuario) {
            return redirect('/psicologia');
        } else {          
            return redirect()->route('loginGET');
        } 

    }

    public function logout(Request $request)
    {
        // LIMPA OS DADOS DA SESSÃO DE USUÁRIO
        session()->forget('usuario');

        // REDIRECIONA PARA TELA DE LOGIN NOVAMENTE
        return view('login');
    }
}
