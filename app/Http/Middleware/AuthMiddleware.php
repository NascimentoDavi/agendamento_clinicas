<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\FaesaClinicaUsuarioGeral; // ajuste conforme seu modelo real
use App\Models\FaesaClinicaUsuario;      // usado no validarADM

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Nome da rota atual
        $routeName = optional($request->route())->getName();

        // Se a rota não tiver nome, deixa passar
        if (!$routeName) {
            return $next($request);
        }

        // Rotas liberadas sem autenticação
        $rotasLiberadas = [
            'loginGET',
            'loginPOST',
            'logout',
        ];

        // Se a rota estiver liberada, segue
        if (in_array($routeName, $rotasLiberadas, true)) {
            if (in_array($routeName, ['loginPOST'], true)) {
                return $this->processarLogin($request, $routeName, $next);
            }
            return $next($request);
        }

        // Já logado?
        if (session()->has('usuario')) {
            return $next($request);
        }

        return redirect()->route('loginGET');
    }

    /**
     * Processa autenticação para as rotas de login POST.
     */
    private function processarLogin(Request $request, string $routeName, Closure $next)
    {
        $credentials = [
            'username' => $request->input('login'),
            'password' => $request->input('senha'),
        ];

        // Chama API
        $response = $this->getApiData($credentials);

        if (!$response['success']) {
            session()->flush();
            return redirect()->back()->with('error', $response['message'] ?? 'Credenciais Inválidas');
        }

        // VALIDA USUARIO NO BANCO
        $validacao = $this->validarUsuario($credentials);

        if (is_null($validacao)) {
            return redirect()->back()->with('error', 'Usuário Inativo');
        } else {
            session(['usuario' => $validacao]);
        }

        return $next($request);
    }

    /**
     * Chamada à API de autenticação.
     */
    private function getApiData(array $credentials): array
    {
        $apiUrl = config('services.faesa.api_url');
        $apiKey = config('services.faesa.api_key');

        try {
            $http = Http::withHeaders([
                'Accept'        => 'application/json',
                'Authorization' => $apiKey,
            ])->timeout(15);

            $resp = $http->post($apiUrl, $credentials);

            if ($resp->successful()) {
                return ['success' => true];
            }

            return [
                'success' => false,
                'message' => $resp->json('message') ?? 'Falha na autenticação',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Valida usuário ativo no banco.
     * Retorna o modelo ou null.
     */
    private function validarUsuario(array $credentials): ?FaesaClinicaUsuario
    {
        $username = $credentials['username'] ?? null;

        if (!$username) {
            return null;
        }

        return FaesaClinicaUsuario::where('ID_USUARIO_CLINICA', $username)
            ->where('SIT_USUARIO', 'Ativo')
            ->first();
    }
}