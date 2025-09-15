<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\FaesaClinicaUsuarioGeral; // ajuste conforme seu modelo real
use App\Models\FaesaClinicaUsuario;      // usado no validarADM

class AuthMiddleware
{
    /**
     * Mapa de configuração para cada tipo de usuário.
     * A chave 'prefix' define a área permitida para cada um.
     */
    private array $userTypeMap = [
        'usuario' => [
            'session' => 'usuario',
            'loginRoute' => 'loginGET',
            'prefix' => 'psicologia', // Apenas rotas com prefixo /psicologia
        ],
        'aluno' => [
            'session' => 'aluno',
            'loginRoute' => 'loginGET',
            'prefix' => 'aluno', // Apenas rotas com prefixo /aluno
        ],
        'professor' => [
            'session' => 'professor',
            'loginRoute' => 'loginGET',
            'prefix' => 'professor', // Apenas rotas com prefixo /professor
        ],
    ];

    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()?->getName();
        $routePrefix = $request->route()?->getPrefix();
        $rotasPublicas = ['loginGET', 'loginPOST', 'logout'];

        // --- LÓGICA PARA USUÁRIOS LOGADOS (AUTORIZAÇÃO) ---

        $activeSessionType = null;
        if (session()->has('usuario'))   $activeSessionType = 'usuario';
        elseif (session()->has('aluno'))     $activeSessionType = 'aluno';
        elseif (session()->has('professor')) $activeSessionType = 'professor';

        if ($activeSessionType) {
            $expectedPrefix = $this->userTypeMap[$activeSessionType]['prefix'];


            // Permite acesso irrestrito às rotas públicas (como o logout)
            if (in_array($routeName, $rotasPublicas)) {
                return $next($request);
            }

            $routePrefix = ltrim($routePrefix, '/');
            // Se o prefixo da rota atual NÃO for o esperado para o tipo de usuário...
            if ($routePrefix !== $expectedPrefix) {
                //Gera erro 403
                abort(403);
            }

            // Se o prefixo for o correto, permite o acesso.
            return $next($request);
        }

        // --- LÓGICA PARA VISITANTES (AUTENTICAÇÃO) ---

        if ($routeName === 'loginPOST') {
            $tipo = $request->input('tipo_usuario');
            if (!$tipo || !array_key_exists($tipo, $this->userTypeMap)) {
                return redirect()->route('loginGET')->with('error', 'Tipo de usuário inválido.');
            }
            return $this->processarLogin($tipo, $request, $next);
        }

        if (in_array($routeName, $rotasPublicas)) {
            return $next($request);
        }

        return redirect()->route('loginGET');
    }

    private function processarLogin(string $tipo, Request $request, Closure $next)
    {
        $sessionKey = $this->userTypeMap[$tipo]['session'];
        $loginRoute = $this->userTypeMap[$tipo]['loginRoute'];

        $credentials = [
            'username' => $request->input('login'),
            'password' => $request->input('senha'),
        ];

        // 1. Autenticação via API
        $response = $this->getApiData($tipo, $credentials);
        if (!$response['success']) {
            return redirect()->route($loginRoute)->with('error', $response['message'] ?? 'Credenciais Inválidas');
        }

        // 2. Validação no banco de dados
        $dadosSessao = match ($tipo) {
            'usuario'   => $this->validarUsuario($credentials),
            'aluno'     => $this->validarAluno($credentials),
            'professor' => $this->validarProfessor($credentials),
        };

        if (!$dadosSessao) {
            return redirect()->route($loginRoute)->with('error', ucfirst($tipo) . ' sem permissão de acesso ao sistema.');
        }

        // 3. Sucesso! Grava os dados na sessão
        session([$sessionKey => $dadosSessao]);

        // Passa a requisição para a próxima etapa (o controller)
        return $next($request);
    }

    /**
     * Chamada à API de autenticação.
     */
    private function getApiData(array $credentials): array
    {
        $apiUrl = match ($tipo) {
            'usuario'   => config('services.faesa.api_url'),
            'aluno'     => config('services.faesa.api_alunos_url'),
            'professor' => config('services.faesa.api_alunos_url'),
        };

        $apiKey = match ($tipo) {
            'usuario'   => config('services.faesa.api_key'),
            'aluno'     => config('services.faesa.api_alunos_key'),
            'professor' => config('services.faesa.api_alunos_key'),
        };

        try {
            if (empty($apiUrl) || empty($apiKey)) {
                return ['success' => false, 'message' => 'API não configurada corretamente no sistema.'];
            }

            $resp = Http::withHeaders([
                'Accept'        => 'application/json',
                'Authorization' => $apiKey,
            ])->timeout(15);

            $resp = $http->post($apiUrl, $credentials);

            return $resp->successful()
                ? ['success' => true, 'data' => $resp->json()]
                : ['success' => false, 'message' => $resp->json('message') ?? 'Falha na autenticação'];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Erro de comunicação: ' . $e->getMessage(),
            ];
        }
    }

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

    private function validarAluno(array $credentials)
    {
        $usuario = $credentials['username'];
        $cpf = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_PESSOA as p')
            ->where('p.WINUSUARIO', 'FAESA\\' . $usuario)
            ->value('CPF');

        $aluno = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_ALUNO as a')
            ->join('LYCEUM_BKP_PRODUCAO.dbo.LY_PESSOA as p', 'p.NOME_COMPL', '=', 'a.NOME_COMPL')
            ->where('p.CPF', $cpf)
            ->where('a.SIT_ALUNO', 'Ativo')
            ->select('a.ALUNO', 'p.NOME_COMPL', 'p.E_MAIL_COM', 'p.CELULAR')
            ->first();

        if ($aluno) {
            $disciplinas = ['D009373', 'D009376', 'D009381', 'D009385', 'D009393', 'D009403', 'D009402', 'D009406', 'D009404'];
            $matricula = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_MATRICULA as m')
                ->where('m.ALUNO', $aluno->ALUNO)
                ->whereIn('m.DISCIPLINA', $disciplinas)
                ->get();

            if (!$matricula->isEmpty()) {
                return [
                    $usuario,
                    $aluno->ALUNO,
                    $aluno->NOME_COMPL,
                    $aluno->E_MAIL_COM,
                    $aluno->CELULAR,
                    $matricula->map(fn($item) => [
                        'DISCIPLINA' => $item->DISCIPLINA,
                        'TURMA'      => $item->TURMA,
                    ])->toArray()
                ];
            }
        }
        return null;
    }

    private function validarProfessor(array $credentials)
    {
        $usuario = $credentials['username'];
        $anoSemestreLetivo = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_OPCOES as o')
            ->where('o.CHAVE', 4)
            ->select('o.ANO_LETIVO', 'o.SEM_LETIVO')
            ->first();

        $cpf = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_PESSOA as p')
            ->where('p.WINUSUARIO', 'FAESA\\' . $usuario)
            ->value('CPF');

        $docente = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_DOCENTE as d')
            ->where('d.CPF', $cpf)
            ->first();

        if ($docente) {
            $disciplinas = ['D009373', 'D009376', 'D009381', 'D009385', 'D009393', 'D009403', 'D009402', 'D009406', 'D009404'];
            $vinculos = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_TURMA as t')
                ->where('t.NUM_FUNC', $docente->NUM_FUNC)
                ->whereIn('t.DISCIPLINA', $disciplinas)
                ->where('t.ANO', $anoSemestreLetivo->ANO_LETIVO)
                ->where('t.SEMESTRE', $anoSemestreLetivo->SEM_LETIVO)
                ->get();

            if (!$vinculos->isEmpty()) {
                return [
                    $usuario,
                    $docente->NUM_FUNC,
                    $docente->NOME_COMPL,
                    $docente->CPF,
                    $vinculos->map(fn($item) => [
                        'DISCIPLINA' => $item->DISCIPLINA,
                        'TURMA'      => $item->TURMA,
                    ])->toArray()
                ];
            }
        }
        return null;
    }
}