<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AuthProfessorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()->getName();

        // ROTAS QUE NAO PRECISA ESTAR LOGADO
        $rotasLiberadas = ['professorLoginGet', 'professorLoginPost', 'professorLogout'];

        // SE A SESSAO JA EXISTIR, PASSA PARA A PROXIMA TELA
        if (session()->has('professor')) {
            return $next($request);
        }

        // SE A ROTA FOR DE POST, PEGA AS CREDENCIAIS
        if ($routeName === 'professorLoginPost') {
            $credentials = [
                'username' => $request->input('login'),
                'password' => $request->input('senha'),
            ];

            $response = $this->getApiData($credentials);

            if ($response['success']) {
                // SE A RESPOSTA FOR SUCESSO, ELE CHAMA A FUNCAO PARA VALIDAR O PROFESSOR
                $validacao = $this->validarProfessor($credentials);

                // SE NAO EXISTIR VALIDACAO, O PROFESSOR NAO TEM ACESSO
                if (!$validacao) {
                    return redirect()->back()->with('error', "Professor sem permissão de acesso");                    
                }

                // RETORNA A SESSAO DO PROFESSOR
                session(['professor' => $validacao]);
                return $next($request);
            }

            session()->flush();
            return redirect()->route('professorLoginGet')->with('error', "Credenciais Inválidas");
        }

        // VERIFICA SE A ROTA E UMAS DAS ROTAS LIBERADAS
        if (!in_array($routeName, $rotasLiberadas)) {
            //dd(session()->all());
            if (!session()->has('professor')) {
                return redirect()->route('professorLoginGet');
            }
        }

        return $next($request);

    }

    //FUNCAO QUE PEGA OS DADOS DA API
    public function getApiData(array $credentials)
    {
        $apiUrl = config('services.faesa.api_alunos_url');
        $apiKey = config('services.faesa.api_alunos_key');

        try {
            $response = Http::withHeaders([
                'Accept' => "application/json",
                'Authorization' => $apiKey
            ])->timeout(5)->post($apiUrl, $credentials);

            if($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Credenciais Inválidas',
                'status'  => $response->status()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    //FUNCAO QUE VALIDA O PROFESSOR
    public function validarProfessor(array $credentials)
    {
        $usuario = $credentials['username'];
        $retorno[0] = $usuario;

        $anoSemestreLetivo = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_OPCOES as o')
        ->where('o.CHAVE', 4)
        ->select('o.ANO_LETIVO', 'o.SEM_LETIVO')
        ->first();

        // BUSCA CPF DO USUÁRIO COM CÓD. DE USUÁRIO
        $cpf = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_PESSOA as p')
        ->where('p.WINUSUARIO', 'FAESA\\' . $usuario)
        ->value('CPF');

        $docente = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_DOCENTE as d')
        ->where('d.CPF', $cpf)
        ->first();

        if($docente) {
            // $disciplinas = $this->buscarDisciplinasClinica();
            $disciplinas = ['D009373', 'D009376', 'D009381', 'D009385', 'D009393', 'D009403', 'D009402', 'D009406', 'D009404'];
            $vinculos = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_TURMA as t')
            ->where('t.NUM_FUNC', $docente->NUM_FUNC)
            ->whereIn('t.DISCIPLINA', $disciplinas)
            ->where('t.ANO', $anoSemestreLetivo->ANO_LETIVO)
            ->where('t.SEMESTRE', $anoSemestreLetivo->SEM_LETIVO)
            ->get();


            if(!$vinculos->isEmpty()) {
                $retorno[] = $docente->NUM_FUNC;
                $retorno[] = $docente->NOME_COMPL;
                $retorno[] = $docente->CPF;

                $retorno[] = ($vinculos->map(function($item) {
                    return [
                        'DISCIPLINA' => $item->DISCIPLINA,
                        'TURMA' => $item->TURMA,
                    ];
                }))->toArray();

                return $retorno;
            } else {
                return null;
            }

        } else {
            return null;
        }
    }

    public function buscarDisciplinasClinica()
    {
        $anoSemestre = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_OPCOES op')->where('op.CHAVE', 4)->select('op.ANO_LETIVO', 'op.SEM_LETIVO')->first();

        $disciplinas = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_TURMA as t')
        ->where('t.FL_FIELD_17', 'CLINICA')->where('t.ANO', $anoSemestre->ANO_LETIVO)->where('op.SEM_LETIVO', $anoSemestre->SEM_LETIVO)->get();
        return $disciplinas->toArray();
    }
}