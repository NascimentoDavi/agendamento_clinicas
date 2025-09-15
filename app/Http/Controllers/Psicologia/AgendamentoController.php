<?php

namespace App\Http\Controllers\Psicologia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FaesaClinicaAgendamento;
use App\Models\FaesaClinicaServico;
use App\Models\FaesaClinicaHorario;
use App\Http\Controllers\Psicologia\PacienteController;
use App\Models\FaesaClinicaaluno;
use App\Models\FaesaClinicaSala;
use App\Services\Psicologia\PacienteService;
use App\Services\Psicologia\AgendamentoService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class AgendamentoController extends Controller
{

    //  INJEÇÃO DE DEPENDÊNCIA
    private PacienteService $pacienteService;
    private AgendamentoService $agendamentoService;

    public function __construct(PacienteService $pacienteService, AgendamentoService $agendamentoService) 
    {
        $this->pacienteService = $pacienteService;
        $this->agendamentoService = $agendamentoService;
    }

    // RETORNA AGENDAMENTOS - ADM
    public function getAgendamento(Request $request)
    {
        $agendamentos = $this->agendamentoService->getAgendamento($request);
        return $agendamentos;
    }

    // RETORNA AGENDAMENTOS - aluno
    public function getAgendamentosForAluno(Request $request)
    {
        $agendamentos = $this->agendamentoService->getAgendamentosForAluno($request);
        return $agendamentos;
    }

    public function getAgendamentosForProfessor(Request $request)
    {
        $agendamentos = $this->agendamentoService->getAgendamentosForProfessor($request);
        return $agendamentos;
    }

    // RETORNA AGENDAMENTOS PARA O CALENDÁRIO
    public function getAgendamentosForCalendar()
    {
        $agendamentos = FaesaClinicaAgendamento::with('paciente', 'servico', 'aluno')
            ->where('ID_CLINICA', 1)
            ->where('STATUS_AGEND', '<>', 'Excluido')
            ->where('STATUS_AGEND', '<>', 'Remarcado')
            ->get();
        
        $events = $agendamentos->map(function($agendamento) {
            $dateOnly = substr($agendamento->DT_AGEND, 0, 10);
            $horaInicio = substr($agendamento->HR_AGEND_INI, 0, 8);
            $horaFim = substr($agendamento->HR_AGEND_FIN, 0, 8);
            $status = $agendamento->STATUS_AGEND;
            $checkPagamento = $agendamento->STATUS_PAG;
            $valorPagamento = $agendamento->VALOR_PAG;

            $start = Carbon::parse("{$dateOnly} {$horaInicio}", 'America/Sao_Paulo')->toIso8601String();
            $end = Carbon::parse("{$dateOnly} {$horaFim}", 'America/Sao_Paulo')->toIso8601String();

            $cor = match($status) {
                'Agendado' => '#0d6efd',
                'Presente' => '#28a745',
                'Cancelado' => '#dc3545',
                default => '#6c757d',
            };

            return [
                'id' => $agendamento->ID_AGENDAMENTO,
                'title' => $agendamento->paciente?->NOME_COMPL_PACIENTE ?? 'Agendamento',
                'aluno' => $agendamento->aluno?->NOME_COMPL ?? 'Não informado',
                'start' => $start,
                'end' => $end,
                'status' => $status,
                'checkPagamento' => $checkPagamento,
                'valorPagamento' => $valorPagamento,
                'servico' => $agendamento->servico?->SERVICO_CLINICA_DESC ?? 'Serviço não informado',
                'description' => $agendamento->OBSERVACOES ?? '',
                'color' => $cor,
                'local' => $agendamento->LOCAL ?? 'Não informado',
            ];
        });

        return response()->json($events);
    }
    
    // RETORNA AGENDAMENTOS PARA O CALENDÁRIO DO aluno
    public function getAgendamentosForCalendaraluno()
    {        
        $aluno = session('aluno');
        $agendamentos = FaesaClinicaAgendamento::with('paciente', 'servico')
        ->where('ID_CLINICA', 1)
        ->where('STATUS_AGEND', '<>', 'Excluido')
        ->where('STATUS_AGEND', '<>', 'Remarcado')
        ->where('ID_ALUNO', $aluno[1] ?? null)
        ->get();
        
        $events = $agendamentos
        ->map(function($agendamento) {
            $dateOnly = substr($agendamento->DT_AGEND, 0, 10);
            $horaInicio = substr($agendamento->HR_AGEND_INI, 0, 8);
            $horaFim = substr($agendamento->HR_AGEND_FIN, 0, 8);
            $status = $agendamento->STATUS_AGEND;
            $checkPagamento = $agendamento->STATUS_PAG;
            $valorPagamento = $agendamento->VALOR_PAG;

            $start = Carbon::parse("{$dateOnly} {$horaInicio}", 'America/Sao_Paulo')->toIso8601String();
            $end = Carbon::parse("{$dateOnly} {$horaFim}", 'America/Sao_Paulo')->toIso8601String();

            $cor = match($status) {
                'Agendado' => '#0d6efd',
                'Presente' => '#28a745',
                'Cancelado' => '#dc3545',
                default => '#6c757d',
            };

            return [
                'id' => $agendamento->ID_AGENDAMENTO,
                'title' => $agendamento->paciente 
                    ? $agendamento->paciente->NOME_COMPL_PACIENTE 
                    : 'Agendamento',
                'start' => $start,
                'end' => $end,
                'status' => $status,
                'checkPagamento' => $checkPagamento,
                'valorPagamento' => $valorPagamento,
                'servico' => $agendamento->servico->SERVICO_CLINICA_DESC ?? 'Serviço não informado',
                'description' => $agendamento->OBSERVACOES ?? '',
                'color' => $cor,
                'local' => $agendamento->LOCAL ?? 'Não informado',
            ];
        });
        return response()->json($events);
    }

    
    // RETORNA AGENDAMENTOS PARA O CALENDÁRIO DE PROFESSOR DE ACORDO COM TURMA
    public function getAgendamentosForCalendarProfessor()
    {
        $professor = session('professor');
        $turmas = array_column($professor[4], 'TURMA');

        $alunos = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_MATRICULA as mat')
        ->join('FAESA_CLINICA_AGENDAMENTO as ag', 'ag.ID_ALUNO', 'mat.ALUNO')
        ->whereIn('mat.TURMA', $turmas)
        ->pluck('ag.ID_ALUNO');

        $agendamentos = FaesaClinicaAgendamento::with('paciente', 'servico')
        ->where('ID_CLINICA', 1)
        ->where('STATUS_AGEND', '<>', 'Excluido')
        ->where('STATUS_AGEND', '<>', 'Remarcado')
        ->whereIn('ID_ALUNO', $alunos)
        ->get();

        $events = $agendamentos
        ->map(function($agendamento) {
            $dateOnly = substr($agendamento->DT_AGEND, 0, 10);
            $horaInicio = substr($agendamento->HR_AGEND_INI, 0, 8);
            $horaFim = substr($agendamento->HR_AGEND_FIN, 0, 8);
            $status = $agendamento->STATUS_AGEND;
            $checkPagamento = $agendamento->STATUS_PAG;
            $valorPagamento = $agendamento->VALOR_PAG;

            $start = Carbon::parse("{$dateOnly} {$horaInicio}", 'America/Sao_Paulo')->toIso8601String();
            $end = Carbon::parse("{$dateOnly} {$horaFim}", 'America/Sao_Paulo')->toIso8601String();

            $cor = match($status) {
                'Agendado' => '#0d6efd',
                'Presente' => '#28a745',
                'Cancelado' => '#dc3545',
                default => '#6c757d',
            };

            return [
                'id' => $agendamento->ID_AGENDAMENTO,
                'title' => $agendamento->paciente 
                    ? $agendamento->paciente->NOME_COMPL_PACIENTE 
                    : 'Agendamento',
                'aluno' => $agendamento->aluno?->NOME_COMPL ?? 'Não informado',
                'start' => $start,
                'end' => $end,
                'status' => $status,
                'checkPagamento' => $checkPagamento,
                'valorPagamento' => $valorPagamento,
                'servico' => $agendamento->servico->SERVICO_CLINICA_DESC ?? 'Serviço não informado',
                'description' => $agendamento->OBSERVACOES ?? '',
                'color' => $cor,
                'local' => $agendamento->LOCAL ?? 'Não informado',
            ];
        });
        return response()->json($events);
    }

    // RETORNA AGENDAMENTOS POR PACIENTE
    public function getAgendamentosByPaciente($idPaciente)
    {
        $agendamentos = FaesaClinicaAgendamento::with(['servico', 'clinica'])
            ->where('ID_CLINICA', 1)
            ->where('ID_PACIENTE', $idPaciente)
            ->where('STATUS_AGEND', '<>', 'Excluido')
            ->where('STATUS_AGEND', '<>', 'Remarcado')
            ->orderBy('DT_AGEND', 'desc')
            ->get();

        return response()->json($agendamentos);
    }

    // CRIAR AGENDAMENTO - ADM | RECEPÇÃO
    public function criarAgendamento(Request $request)
    {
        // VALIDACAO DOS DADOS
        $validatedData = $request->validate([
            'paciente_id' => 'required|integer',
            'usuario_id' => 'required|string',
            'id_servico' => 'required|integer',
            'dia_agend' => 'required|date',
            'hr_ini' => 'required',
            'hr_fim' => 'required|after:hr_ini',
            'ID_ALUNO' => 'nullable|integer',
            'id_sala_clinica' => 'nullable|integer|exists:faesa_clinica_sala,ID_SALA_CLINICA',
            'tem_recorrencia' => 'nullable|string',
            'dias_semana' => 'nullable|array',
            'dias_semana.*' => 'in:0,1,2,3,4,5,6',
            'data_fim_recorrencia' => 'nullable|date|after_or_equal:dia_agend',
            'duracao_meses_recorrencia' => 'nullable|integer|min:1',
            'valor_agend' => 'nullable|string',
        ], [
            'paciente_id.required' => 'A seleção de paciente é obrigatória.',
            'id_servico.required' => 'A seleção de serviço obrigatória.',
            'dia_agend.required' => 'A data do agendamento é obrigatória.',
            'hr_ini.required' => 'A hora de início é obrigatória.',
            'hr_fim.required' => 'A hora de término é obrigatória.',
            'hr_fim.after' => 'A hora de término deve ser posterior à hora de início.',
            'id_sala_clinica.exists' => 'A sala selecionada não existe.',
        ]);
    
        if (!empty($validatedData['valor_agend'])) {
            $validatedData['valor_agend'] = str_replace(',', '.', $validatedData['valor_agend']);
        }

        try {
            $resultado = $this->agendamentoService->criarAgendamentos($validatedData);

            // 3. Trata a resposta do serviço
            if ($resultado['criados'] === 0 && !empty($resultado['erros'])) {
                // Se NENHUM foi criado, mostra o erro detalhado do primeiro dia.
                $primeiroErro = reset($resultado['erros']);
                return redirect()->back()->withInput()
                    ->with('error', "Nenhum agendamento criado. Motivo: " . $primeiroErro);

            } elseif (!empty($resultado['erros'])) {
                // Se ALGUNS foram criados, mostra uma mensagem de sucesso parcial.
                $diasProblematicos = implode(', ', array_keys($resultado['erros']));
                return redirect('/psicologia/criar-agendamento/')
                    ->with('success', "Agendamentos criados, exceto para os dias: $diasProblematicos devido a conflitos.");

            } else {
                // Se TODOS foram criados com sucesso.
                return redirect('/psicologia/criar-agendamento/')
                    ->with('success', 'Todos os agendamentos foram criados com sucesso!');
            }

        } catch (\Exception $e) {
            // Captura qualquer exceção inesperada do serviço
            return redirect()->back()->withInput()->with('error', 'Ocorreu um erro inesperado: ' . $e->getMessage());
        }
    }

    // CRIAR AGENDAMENTO - aluno
    public function criarAgendamentoAluno(Request $request)
    {
        // VALIDAÇÃO INICIAL
        $validatedData = $request->validate([
            'paciente_id' => 'required|integer',
            'usuario_id' => 'required|string',
            'id_servico' => 'required|integer',
            'dia_agend' => 'required|date',
            'hr_ini' => 'required',
            'hr_fim' => 'required|after:hr_ini',
            'ID_ALUNO' => 'required|integer',
            'id_sala_clinica' => 'nullable|integer|exists:faesa_clinica_sala,ID_SALA_CLINICA',
            'local_agend' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ], [
            'paciente_id.required' => 'A seleção de paciente é obrigatória.',
            'id_servico.required' => 'A seleção de serviço obrigatória.',
            'dia_agend.required' => 'A data do agendamento é obrigatória.',
            'hr_ini.required' => 'A hora de início é obrigatória.',
            'hr_fim.required' => 'A hora de término é obrigatória.',
            'hr_fim.after' => 'A hora de término deve ser posterior à hora de início.',
            'id_sala_clinica.exists' => 'A sala selecionada não existe.',
            'ID_ALUNO.required' => 'A identificação do aluno é necessária.',
        ]);

        // Chama o mesmo service que o método criarAgendamento
        try {
            $resultado = $this->agendamentoService->criarAgendamentos([
                'paciente_id' => $validatedData['paciente_id'],
                'usuario_id' => $validatedData['usuario_id'],
                'id_servico' => $validatedData['id_servico'],
                'dia_agend' => $validatedData['dia_agend'],
                'hr_ini' => $validatedData['hr_ini'],
                'hr_fim' => $validatedData['hr_fim'],
                'ID_ALUNO' => $validatedData['ID_ALUNO'],
                'id_sala_clinica' => $validatedData['id_sala_clinica'] ?? null,
                'local_agend' => $validatedData['local_agend'] ?? null,
                'observacoes' => $validatedData['observacoes'] ?? null,
            ]);

            // 1. Nenhum agendamento criado
            if ($resultado['criados'] === 0 && !empty($resultado['erros'])) {
                $primeiroErro = reset($resultado['erros']);
                return redirect()->back()->withInput()
                    ->with('error', "Nenhum agendamento criado. Motivo: " . $primeiroErro);
            }

            // 2. Alguns agendamentos criados, outros com conflito
            if (!empty($resultado['erros'])) {
                $diasProblematicos = implode(', ', array_keys($resultado['erros']));
                return redirect()->back()
                    ->with('success', "Agendamentos criados, exceto para os dias: $diasProblematicos devido a conflitos.");
            }

            // 3. Todos os agendamentos criados com sucesso
            return redirect()->back()->with('success', 'Agendamento criado com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Ocorreu um erro inesperado: ' . $e->getMessage());
        }
    }

    // MOSTRA AGENDAMENTOS RETORNANDO VIEW - Utiliza Injeção de Dependência
    public function showAgendamento($id)
    {
        $agendamento = FaesaClinicaAgendamento::findOrFail($id);

        $lista_alunos = DB::table('FAESA_CLINICA_AGENDAMENTO')
            ->select('ID_ALUNO')
            ->whereNotNull('ID_ALUNO')
            ->distinct()
            ->get();

        return view('psicologia.adm.agendamento_show', compact('agendamento', 'lista_alunos'));
    }

    // RETORNA VIEW DE EDIÇÃO DE AGENDAMENTO - Utiliza Injeção de Dependência
    public function editAgendamento($id)
    {
        $agendamento = FaesaClinicaAgendamento::with('paciente', 'servico', 'aluno')
        ->findOrFail($id);
        return view('psicologia.adm.editar_agendamento', compact('agendamento'));
    }

    public function editAgendamentoaluno($id, FaesaClinicaAgendamento $agendamentoModel)
    {
        $agendamento = $agendamentoModel->with('paciente', 'servico', 'aluno')->findOrFail($id);

        $idUsuario = session('aluno')[0];
        if($idUsuario === $agendamento->ID_USUARIO){
            return view('psicologia.aluno.editar_agendamento', compact('agendamento'));
        } else {
            return redirect()->route('alunoConsultarAgendamentos-GET')->with('error', 'Você não tem permissão para editar este agendamento.');
        }        
    }

    // CONTROLLER DE EDIÇÃO DE AGENDAMENTO - Utiliza Injeção de Dependência
    public function updateAgendamento(Request $request)
    {
        $validatedData = $request->validate([
            'ID_AGENDAMENTO' => 'required|integer|exists:faesa_clinica_agendamento,ID_AGENDAMENTO',
            'ID_SERVICO'     => 'required|integer',
            'ID_PACIENTE'    => 'required|integer',
            'ID_ALUNO'   => 'nullable|integer',
            'ID_SALA'        => 'nullable|integer',
            'DT_AGEND'       => 'required|date_format:Y-m-d',
            'HR_AGEND_INI'   => 'required|date_format:H:i',
            'HR_AGEND_FIN'   => 'required|date_format:H:i|after:HR_AGEND_INI',
            'STATUS_AGEND'   => 'required|string',
            'VALOR_AGEND'    => 'nullable|string',
            'OBSERVACOES'    => 'nullable|string',
        ], [
            'ID_SERVICO.required'  => 'É obrigatório informar o serviço para prosseguir com a edição',
            'ID_PACIENTE.required' => 'É obrigatório informar o paciente para prosseguir com a edição',
            'HR_AGEND_FIN.after'   => 'O horário final deve ser posterior ao horário inicial.',
            'ID_AGENDAMENTO.exists' => 'O agendamento que você está tentando editar não foi encontrado.',
        ]);

        try {
            $agendamento = $this->agendamentoService->atualizarAgendamento($validatedData);
            
            $mensagem = $agendamento->wasRecentlyCreated 
                ? 'Agendamento remarcado com sucesso! Um novo agendamento foi criado.'
                : 'Agendamento atualizado com sucesso!';

            if (Route::current()->uri() == 'psicologia/agendamento') {
                return redirect()->route('listagem-agendamentos')->with('success', $mensagem);
            } else if (Route::current()->uri() == 'aluno/agendamento') {
                return redirect()->route('alunoConsultarAgendamentos-GET')->with('success', $mensagem);
            } else {
                return redirect()->route('professorConsultarAgendamentos-GET')->with('success', $mensagem);
            }

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ATUALIZA STATUS DO AGENDAMENTO
    public function atualizarStatus(Request $request, $id)
    {
        $agendamento = FaesaClinicaAgendamento::find($id);

        // CASO NÃO ENCONTRE AGENDAMENTO
        if (!$agendamento) {
            return response()->json(['message' => 'Agendamento não encontrado'], 404);
        }

        // ZERA O VALOR CASO O CHECK SEJA MARCADO COMO NÃO PAGO
        if($request->checkPagamento == 'N') {
            $request->merge(['valorPagamento' => 0.00]);
        }

        $request->validate([
            'status' => 'required|in:Agendado,Presente,Finalizado,Cancelado',
            'checkPagamento' => 'in:S,N',
            'valorPagamento' => 'required_if:checkPagamento,S|numeric',
        ], [
            'valorPagamento.required_if' => 'O campo valor do pagamento é obrigatório quando o pagamento está marcado como realizado.',
            'valorPagamento.numeric' => 'O campo valor do pagamento deve ser um número válido.',
        ]);

        // FORMATA VALOR DO PAGAMENTO CASO O TENHA
        if($request->has('valorPagamento')) {
            $request->merge([
                'valorPagamento' => str_replace(',', '.', $request->valorPagamento),
            ]);
        }

        $agendamento->STATUS_AGEND = $request->status;
        $agendamento->STATUS_PAG = $request->checkPagamento;
        $agendamento->VALOR_PAG = $request->valorPagamento;

        $agendamento->save();

        return response()->json(['message' => 'Agendamento atualizado com sucesso']);
    }

    // FUNÇÃO DE EXCLUSÃO DE AGENDAMENTO
    public function deleteAgendamento(Request $request, $id)
    {
        $agendamento = FaesaClinicaAgendamento::findOrFail($id);

        $agendamento->STATUS_AGEND = "Excluido";
        $agendamento->save();

        // Volta para a página anterior
        return redirect()->back()->with('success', 'Agendamento excluído com sucesso');
    }

    public function AlunoDeleteAgendamento(Request $request, $id, FaesaClinicaAgendamento $agendamentoModel)
    {
        $agendamento = $agendamentoModel->with('paciente', 'servico', 'aluno')->findOrFail($id);

        $idUsuario = session('aluno')[0];
        if($idUsuario === $agendamento->ID_USUARIO){
            $agendamento->STATUS_AGEND = "Excluido";
            $agendamento->save();
        } else {
            // Volta para a página anterior
            return redirect()->back()->with('error', 'Você esqueceu que é pobre? Tu não tem acesso aqui não');
        } 

        // Volta para a página anterior
        return redirect()->back()->with('success', 'Agendamento excluído com sucesso');
    }

    // VERIFICA CONFLITOS DE AGENDAMENTO
    private function existeConflitoAgendamento(
        int $idClinica, 
        ?int $idSala, 
        string $data, 
        string $horaInicio, 
        string $horaFim, 
        int $idPaciente, 
        ?int $idaluno, 
        ?int $idAgendamentoParaIgnorar = null
    ): bool
    {
        // Inicia a consulta com os filtros de data e hora
        $conflitoQuery = FaesaClinicaAgendamento::where('ID_CLINICA', $idClinica)
            ->where('DT_AGEND', $data)
            ->where('HR_AGEND_INI', '<', $horaFim)
            ->where('HR_AGEND_FIN', '>', $horaInicio)
            ->where('STATUS_AGEND', '<>', 'Excluido');

        // EVITA AGENDAMENTO QUE ESTÁ SENDO ATUALIZADO, SE FOR O CASO
        if ($idAgendamentoParaIgnorar) {
            $conflitoQuery->where('ID_AGENDAMENTO', '!=', $idAgendamentoParaIgnorar);
        }

        $conflitoQuery->where(function ($query) use ($idPaciente, $idSala, $idaluno) {
            $query->where('ID_PACIENTE', $idPaciente);

            if ($idSala) {
                $query->orWhere('ID_SALA', $idSala);
            }
            
            if ($idaluno) {
                $query->orWhere('ID_ALUNO', $idaluno);
            }
        });

        return $conflitoQuery->exists();
    }   

    // VERIFICA CONFLITO EXCLUSIVO DO PACIENTE
    private function existeConflitoPaciente($idClinica, $idPaciente, $dataAgend, $hrIni, $hrFim, $idAgendamentoAtual = null)
    {
        // FORMATA VALORES
        $dataAgend = Carbon::parse($dataAgend)->format('Y-m-d');
        $hrIni = Carbon::parse($hrIni)->format('H:i:s');
        $hrFim = Carbon::parse($hrFim)->format('H:i:s');

        $query = FaesaClinicaAgendamento::where('ID_CLINICA', $idClinica)
            ->where('ID_PACIENTE', $idPaciente)
            ->where('DT_AGEND', $dataAgend)

            // VERIFICA SOBREPOSIÇÃO DE HORÁRIO
            ->where(function($q) use ($hrIni, $hrFim) {
                $q->where('HR_AGEND_INI', '<', $hrFim)
                ->where('HR_AGEND_FIN', '>', $hrIni);
            })
            ->where('STATUS_AGEND', '<>', 'Excluido')
            ->where('STATUS_AGEND', '<>', 'Remarcado');

        // EVITA QUE ACUSE DE ERRO COM O PRÓPRIO AGENDAMENTO
        if ($idAgendamentoAtual) {
            $query->where('ID_AGENDAMENTO', '<>', $idAgendamentoAtual);
        }        
        return $query->exists();
    }

    // VERIFICA SE SALA ESTÁ ATIVA
    private function salaEstaAtiva($salaAgendamento)
    {
        if ($salaAgendamento == null) {
            return true;
        }

        // BUSCA TODAS AS SALAS INATIVAS DA CLÍNICA
        $salasInativas = FaesaClinicaSala::where('ATIVO', 'N')->get();

        // Verifica se alguma dessas salas tem a descrição igual à sala do agendamento
        foreach ($salasInativas as $sala) {
            if ($sala->ID_SALA_CLINICA == $salaAgendamento) {
                return false;
            }
        }
        return true;
    }

    // VERIFICA HORÁRIOS DISPONÍVEIS
    private function horarioEstaDisponivel($idClinica, $dataAgendamento, $horaInicio, $horaFim)
    {
        $data = Carbon::parse($dataAgendamento)->format('Y-m-d');
        $horaInicio = Carbon::parse($horaInicio)->format('H:i:s');
        $horaFim = Carbon::parse($horaFim)->format('H:i:s');

        // VERIFICA BLOQUEIOS
        $bloqueios = FaesaClinicaHorario::where('ID_CLINICA', $idClinica)
            ->where('BLOQUEADO', 'S')
            ->whereDate('DATA_HORARIO_INICIAL', '<=', $data)
            ->whereDate('DATA_HORARIO_FINAL', '>=', $data)
            ->whereTime('HR_HORARIO_INICIAL', '<', $horaFim)
            ->whereTime('HR_HORARIO_FINAL', '>', $horaInicio)
            ->exists();

        if ($bloqueios) return false;

        // VERIFICA SE EXISTE ALGUM HORÁRIO PERMITIDO
        $horariosPermitidos = FaesaClinicaHorario::where('ID_CLINICA', $idClinica)
            ->where('BLOQUEADO', 'N')
            ->whereDate('DATA_HORARIO_INICIAL', '<=', $data)
            ->whereDate('DATA_HORARIO_FINAL', '>=', $data)
            ->get();

        // Se não tiver horários permitidos cadastrados, considera disponível
        if ($horariosPermitidos->isEmpty()) return true;

        foreach ($horariosPermitidos as $horario) {
            $inicioPermitido = Carbon::parse($horario->HR_HORARIO_INICIAL)->format('H:i:s');
            $fimPermitido = Carbon::parse($horario->HR_HORARIO_FINAL)->format('H:i:s');

            if ($horaInicio >= $inicioPermitido && $horaFim <= $fimPermitido) {
                return true;
            }
        }
        return false;
    }

    // ADICIONA MENSAGEM DE MOTIVO DE CANCELAMENTO AO AGENDAMENTO
    public function addMensagemCancelamento(Request $request)
    {
        $this->agendamentoService->addMensagemCancelamento(
            $request->id,
            $request->mensagem,
            $request->checkPagamento,
            $request->valorPagamento
        );

        return response()->json([
            'success' => true,
            'message' => 'Mensagem de Cancelamento adicionada com sucesso!'
        ]);
    }

    // VERIFICA FERIADO PARA NÃO PERMITIR AGENDA
    private function verificaFeriado($data)
    {   
        $data = Carbon::parse($data)->format('Y-m-d');
        $dataFeriado = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_FERIADO')
            ->where('DATA', '=', $data )
            ->get();

        if ($dataFeriado->isNotEmpty()){
            return true;
        }

        return false;
    }

    // VALIDA E CRIA AGENDAMENTO ÚNICO
    private function validarECriarAgendamentoUnico(array $dadosAgendamento): ?string
    {
        // 1. Verifica a disponibilidade GERAL do horário primeiro.
        if (!$this->horarioEstaDisponivel($dadosAgendamento['ID_CLINICA'], $dadosAgendamento['DT_AGEND'], $dadosAgendamento['HR_AGEND_INI'], $dadosAgendamento['HR_AGEND_FIN'])) {
            return "Horário indisponível"; // Mensagem específica
        }

        // 2. Verifica se a sala está ativa (se houver sala).
        if (isset($dadosAgendamento['ID_SALA']) && !$this->salaEstaAtiva($dadosAgendamento['ID_SALA'])) {
            return "Sala inativa"; // Mensagem específica
        }

        // 3. Por último, verifica conflitos ESPECÍFICOS com outros agendamentos.
        if ($this->existeConflitoAgendamento(
            $dadosAgendamento['ID_CLINICA'],
            $dadosAgendamento['ID_SALA'],
            $dadosAgendamento['DT_AGEND'],
            $dadosAgendamento['HR_AGEND_INI'],
            $dadosAgendamento['HR_AGEND_FIN'],
            $dadosAgendamento['ID_PACIENTE'],
            $dadosAgendamento['ID_ALUNO']
        )) {
            return "Conflito de agendamento"; // Mensagem específica
        }

        // Se tudo estiver OK, cria o agendamento.
        FaesaClinicaAgendamento::create($dadosAgendamento);

        return null; // Sucesso
    }
}