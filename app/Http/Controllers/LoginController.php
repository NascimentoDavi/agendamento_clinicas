<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    /**
     * Mostra o formulário de login ou redireciona o usuário se ele já estiver logado.
     * Este método substitui a closure da sua rota 'loginGET'.
     */
    public function showLoginForm(Request $request)
    {
        // Redireciona CADA tipo de usuário para seu respectivo dashboard
        if (session('aluno')) {
            // A rota 'alunoAgenda' está dentro do prefixo '/aluno'
            return redirect()->route('alunoAgenda');
        } 
        
        if (session('professor')) {
            // A rota 'professorMenu' está dentro do prefixo '/professor'
            return redirect()->route('professorMenu');
        } 
        
        if (session('usuario')) {
            // A rota 'menu_agenda_psicologia' está dentro do prefixo '/psicologia'
            return redirect()->route('menu_agenda_psicologia');
        } 
        
        // Se ninguém estiver logado, apenas mostra a view de login
        return view('login');
    }

    /**
     * Recebe a requisição após o AuthMiddleware validar o login.
     * Este método substitui a closure vazia da sua rota 'loginPOST'.
     * Sua única função é redirecionar o usuário para o dashboard correto.
     */
    public function handleLogin(Request $request): RedirectResponse
    {
        // O middleware já criou a sessão. A lógica aqui é a mesma de cima:
        // olhar a sessão e redirecionar para a rota com o prefixo correto.
        if (session('aluno')) {
            return redirect()->route('alunoAgenda');
        } 
        
        if (session('professor')) {
            return redirect()->route('professorMenu');
        } 
        
        if (session('usuario')) {
            return redirect()->route('menu_agenda_psicologia');
        } 
        
        // Se algo der muito errado e não houver sessão, volta ao login.
        return redirect()->route('loginGET')->with('error', 'Ocorreu um erro inesperado.');
    }

    /**
     * Realiza o logout de QUALQUER tipo de usuário.
     * Este método substitui a closure da sua rota 'logout'.
     */
    public function handleLogout(Request $request): RedirectResponse
    {
        session()->forget(['usuario', 'aluno', 'professor']);
        return redirect()->route('loginGET');
    }
}