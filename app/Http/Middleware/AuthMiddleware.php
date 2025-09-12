<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\FaesaClinicaUsuario;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $routeName  = optional($request->route())->getName();
        $prefix     = $request->route()?->getPrefix();

        if (!$routeName) {
            return $next($request);
        }

        // Regras por tipo de usuário
        $map = [
            'usuario' => [
                'session' => 'usuario',
                'rotasLiberadas' => ['loginGET', 'loginPOST', 'logout'],
                'loginRoute' => 'loginGET',
            ],
            'aluno' => [
                'session' => 'aluno',
                'rotasLiberadas' => ['alunoLoginGet', 'alunoLoginPost', 'alunoLogout'],
                'loginRoute' => 'alunoLoginGet',
            ],
            'professor' => [
                'session' => 'professor',
                'rotasLiberadas' => ['professorLoginGet', 'professorLoginPost', 'professorLogout'],
                'loginRoute' => 'professorLoginGet',
            ],
        ];

        // Detecta o contexto (pelo prefixo da rota ou pelo nome)
        $tipo = $this->detectarTipo($routeName, $prefix, $map);

        if (!$tipo) {
            return $next($request);
        }

        $sessionKey    = $map[$tipo]['session'];
        $rotasLiberadas = $map[$tipo]['rotasLiberadas'];
        $loginRoute     = $map[$tipo]['loginRoute'];

        // Se já tem sessão válida
        if (session()->has($sessionKey)) {
            return $next($request);
        }

        // Se for rota de login POST
        if ($this->isLoginPost($tipo, $routeName)) {
            return $this->processarLogin($tipo, $request, $next, $sessionKey, $loginRoute);
        }

        // Se a rota não estiver liberada e não tiver sessão → redireciona
        if (!in_array($routeName, $rotasLiberadas)) {
            return redirect()->route($loginRoute);
        }

        return $next($request);
    }

    private function detectarTipo(?string $routeName, ?string $prefix, array $map): ?string
    {
        foreach ($map as $tipo => $dados) {
            foreach ($dados['rotasLiberadas'] as $rota) {
                if (str_starts_with($routeName, $tipo) || $routeName === $rota || $prefix === $tipo) {
                    return $tipo;
                }
            }
        }
        return null;
    }

    private function isLoginPost(string $tipo, string $routeName): bool
    {
        return match ($tipo) {
            'usuario'   => $routeName === 'loginPOST',
            'aluno'     => $routeName === 'alunoLoginPost',
            'professor' => $routeName === 'professorLoginPost',
            default     => false,
        };
    }

    private function processarLogin(string $tipo, Request $request, Closure $next, string $sessionKey, string $loginRoute)
    {
        $credentials = [
            'username' => $request->input('login'),
            'password' => $request->input('senha'),
        ];

        $response = $this->getApiData($tipo, $credentials);

        if (!$response['success']) {
            session()->flush();
            return redirect()->route($loginRoute)->with('error', $response['message'] ?? 'Credenciais Inválidas');
        }

        // Validação específica por tipo
        $validacao = match ($tipo) {
            'usuario'   => $this->validarUsuario($credentials),
            'aluno'     => $this->validarAluno($credentials),
            'professor' => $this->validarProfessor($credentials),
            default     => null,
        };

        if (!$validacao) {
            return redirect()->route($loginRoute)->with('error', ucfirst($tipo) . ' sem permissão de acesso');
        }

        // Grava na sessão
        session([$sessionKey => $validacao]);

        return $next($request);
    }

    private function getApiData(string $tipo, array $credentials): array
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
            $resp = Http::withHeaders([
                'Accept'        => 'application/json',
                'Authorization' => $apiKey,
            ])->timeout(10)->post($apiUrl, $credentials);

            if ($resp->successful()) {
                return ['success' => true, 'data' => $resp->json()];
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

    // ====================== Validações =======================

    private function validarUsuario(array $credentials): ?FaesaClinicaUsuario
    {
        $username = $credentials['username'] ?? null;
        if (!$username) return null;

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
