<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\FaesaClinicaUsuario;
use App\Models\FaesaClinicaUsuarioGeral;
use Illuminate\Support\Facades\DB;
use stdClass;

use function PHPUnit\Framework\isEmpty;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // ARMAZENA ROTA QUE USUARIO QUER ACESSAR
        $routeName = $request->route()->getName();

        if(!$routeName) {
            return $next($request);
        }

        // CASO A ROTA QUE O USUÁRIO TENTA ACESSAR SEJA ALGUMA DESSAS, ELE PERMITE SEGUIR ADIANTE
        if(in_array($routeName, [
            'loginGET',
            'logout',
            'psicologoLoginGet',
            'professorLoginGet',
            'psicologoLogout',
            'professorLogout',
        ])){
            return $next($request);
        }

        // AUTENTICAÇÃO VIA POST
        if( ($routeName === 'loginPOST') ||  ($routeName === 'psicologoLoginPost')) {

            // ARMAZENA CREDENCIAIS
            $credentials = [
                'username' => $request->input('login'),
                'password' => $request->input('senha'),
            ];

            // DEPENDENDO DA ROTA, CHAMA UMA API DIFERENTE PARA AUTENTICAÇÃO
            $response = $routeName === 'psicologoLoginPost'
            ? $this->apiData($credentials)
            : $this->apiAdm($credentials);

            if($response['success']) {

                // VALIDA DISCIPLINA DO USUÁRIO
                $validacao = $this->validar($credentials);

                if($routeName)

                if (!$validacao) {

                    return redirect()->back()->with('error', "Usuário não tem matrícula nas disciplinas da clínica");
                }
            }
        }
    }

    // ALUNO E PROFESSOR
    public function apiData(array $credentials): array
    {
        // CREDENCIAIS DA API
        $apiUrl = config('services.faesa.api_psicologos_url');
        $apiKey = config('services.faesa.api_psicologos_key');

        try {
            $response = Http::withHeaders([
                'Accept' => "application/json",
                'Authorization' => $apiKey
            ])
            ->post($apiUrl, $credentials);

            if($response->successful()) {

                return [
                    'success' => true
                ];
                
            } else {

                return [
                    'success' => false
                ];
                
            }
            
        } catch (\Exception $e) {
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
            
        }
    }


    // USUÁRIO ADM
    public function apiAdm(array $credentials): array
    {
        $apiUrl = config('services.faesa.api_url');
        $apiKey = config('services.faesa.api_key');
        
        try {
            $response = Http::withHeaders([
                'Accept'        => "application/json",
                'Authorization' => $apiKey
            ])
            ->timeout(5)
            ->post($apiUrl, $credentials);

            if($response->successful()) {
                return [
                    'success' => true
                ];
            } else {
                return [
                    'success' => false
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function validar(array $credentials)
    {
        $usuario = $credentials['username'];
        $retorno[0] = $usuario;

        $cpf = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_PESSOA as p')
        ->where('p.WINUSUARIO', 'FAESA\\' . $usuario)
        ->value('CPF');

        // VERIFICA SE É ALUNO OU DOCENTE
        $aluno = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_ALUNO as a')
        ->join('LYCEUM_BKP_PRODUCAO.dbo.LY_PESSOA as p', 'p.NOME_COMPL', '=', 'a.NOME_COMPL')
        ->where('p.CPF', $cpf)
        ->where('a.SIT_ALUNO', 'Ativo')
        ->select('a.ALUNO', 'p.NOME_COMPL', 'p.E_MAIL_COM', 'p.CELULAR')
        ->first();

        if($aluno) {
            // NO MOMENTO HARDCORDED, MAS PREPARAR CONSULTA PARA PEGAR DO FL_FIELD_17
            $disciplinas = ['D009373', 'D009376', 'D009381', 'D009385', 'D009393', 'D009403', 'D009402', 'D009406', 'D009404'];

            $matricula = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_MATRICULA as m')
            ->where('m.ALUNO', $aluno->ALUNO)
            ->whereIn('m.DISCIPLINA', $disciplinas)
            ->get();

            // SE POSSUI MATRICULA NAS DISCIPLINAS DE ESTÁGIO
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
                $docente = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_DOCENTE as d')
                ->where('d.CPF', $cpf)
                ->first();

                if($docente) {
                    $retorno[] = $docente->NUM_FUNC;
                    $retorno[] = $docente->NOME_COMPL;
                    $retorno[] = $docente->E_MAIL;
                    $retorno[] = $docente->FONE;
                    $retorno[] = $docente->PESSOA;

                    return $retorno;
                } else {
                    return null;
                }
            }
        } else {
            $docente = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_DOCENTE as d')
            ->where('d.CPF', $cpf)
            ->first();

            if($docente) {
                $retorno[] = $docente->NUM_FUNC;
                $retorno[] = $docente->NOME_COMPL;
                $retorno[] = $docente->E_MAIL;
                $retorno[] = $docente->FONE;
                $retorno[] = $docente->PESSOA;

                return $retorno;
            } else {
                return null;
            }
        }
    }
}
