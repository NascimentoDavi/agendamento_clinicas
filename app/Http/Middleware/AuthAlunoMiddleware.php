<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AuthAlunoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()->getName();

        $rotasLiberadas = ['alunoLoginGet', 'alunoLoginPost', 'alunoLogout'];

        // CASO JÁ TENHA SESSÃO, REDIRECIONA PARA MENU
        if (session()->has('aluno')) {
            return $next($request);
        }

        // SE A ROTA FOR DE POST
        if ($routeName === 'alunoLoginPost') {

            // ARMAZENA CREDENCIAIS
            $credentials = [
                'username' => $request->input('login'),
                'password' => $request->input('senha'),
            ];

            // ARMAZENA RESPOSTA DA API
            $response = $this->getApiData($credentials);

            if ($response['success']) {
                $validacao = $this->validaraluno($credentials);

                if (!$validacao) {
                    return redirect()->back()->with('error', "Aluno sem permissão de acesso");
                }

                // SALVA O aluno NA SESSÃO NA CHAVE 'aluno'
                session(['aluno' => $validacao]);

                return $next($request);
            }

            session()->flush();
            return redirect()->route('alunoLoginGet')->with('error', "Credenciais Inválidas");
        }

        if (!in_array($routeName, $rotasLiberadas)) {
            if (!session()->has('aluno')) {
                return redirect()->route('alunoLoginGet');
            }
        }

        return $next($request);
    }

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

    // VALIDA USUÁRIO aluno
    public function validaraluno(array $credentials)
    {
        $usuario = $credentials['username'];
        $retorno[0] = $usuario;

        // BUSCA CPF DO USUÁRIO COM CÓD. DE USUÁRIO
        $cpf = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_PESSOA as p')
        ->where('p.WINUSUARIO', 'FAESA\\' . $usuario)
        ->value('CPF');

        // VERIFICA SE É ALUNO
        $aluno = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_ALUNO as a')
        ->join('LYCEUM_BKP_PRODUCAO.dbo.LY_PESSOA as p', 'p.NOME_COMPL', '=', 'a.NOME_COMPL')
        ->where('p.CPF', $cpf)
        ->where('a.SIT_ALUNO', 'Ativo')
        ->select('a.ALUNO', 'p.NOME_COMPL', 'p.E_MAIL_COM', 'p.CELULAR')
        ->first();

        if($aluno) {
            // $disciplinas = $this->buscarDisciplinasClinica();
            $disciplinas = ['D009373', 'D009376', 'D009381', 'D009385', 'D009393', 'D009403', 'D009402', 'D009406', 'D009404'];
            $matricula = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_MATRICULA as m')
            ->where('m.ALUNO', $aluno->ALUNO)
            ->whereIn('m.DISCIPLINA', $disciplinas)
            ->get();

            if(!$matricula->isEmpty()) {

                $retorno[] = $aluno->ALUNO;
                $retorno[] = $aluno->NOME_COMPL;
                $retorno[] = $aluno->E_MAIL_COM;
                $retorno[] = $aluno->CELULAR;

                $retorno[] = ($matricula->map(function($item) {
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