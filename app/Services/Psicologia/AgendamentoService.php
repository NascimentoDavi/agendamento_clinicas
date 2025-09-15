<?php

namespace App\Services\Psicologia;

use App\Models\FaesaClinicaAgendamento;
use App\Models\FaesaClinicaServico;
use App\Models\FaesaClinicaSala;
use App\Models\FaesaClinicaHorario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use function PHPUnit\Framework\isNumeric;

class AgendamentoService
{
    const ID_CLINICA = 1;

    // RETORNA AGENDAMENTOS PARA ADM
    public function getAgendamento(Request $request)
    {
        $query = FaesaClinicaAgendamento::with([
            'paciente',
            'servico',
            'clinica',
            'agendamentoOriginal',
            'remarcacoes',
            'aluno'
        ])
        ->where('ID_CLINICA', 1)
        ->where('STATUS_AGEND', '<>', 'Excluido');

        // Filtro por nome ou CPF do paciente
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('paciente', function($q) use ($search) {
                $q->where('NOME_COMPL_PACIENTE', 'like', "%{$search}%")
                ->orWhere('CPF_PACIENTE', 'like', "%{$search}%");
            });
        }

        // FILTRO POR ALUNO
        if ($request->filled('aluno')) {
            $aluno = $request->input('aluno');

            $query->whereHas('aluno', function($q) use ($aluno) {
                $q->where('ALUNO', 'like', "{$aluno}%")
                ->orWhere('NOME_COMPL', 'like', "%{$aluno}%");
            });
        }

        // FILTRO POR DATA
        if ($request->filled('date')) {
            try {
                $date = Carbon::parse($request->input('date'))->format('Y-m-d');
                $query->where('DT_AGEND', $date);
            } catch (\Exception $e) {
                // DATA INVÁLIDA - IGNORA FILTRO
            }
        }

        // FILTRO POR HORA DE INÍCIO
        if ($request->filled('start_time')) {
            try {
                $startTime = Carbon::createFromFormat('H:i', $request->input('start_time'))->format('H:i:s');
                $query->where('HR_AGEND_INI', '>=', $startTime);
            } catch (\Exception $e) {
                // Hora inválida - ignora filtro
            }
        }

        // FILTRO POR HORA DE FIM
        if ($request->filled('end_time')) {
            try {
                $endTime = Carbon::createFromFormat('H:i', $request->input('end_time'))->format('H:i:s');
                $query->where('HR_AGEND_FIN', '<=', $endTime);
            } catch (\Exception $e) {
                // Hora inválida - ignora filtro
            }
        }

        // FILTRO POR STATUS
        if ($request->filled('status')) {
            $query->where('STATUS_AGEND', $request->input('status'));
        }

        // FILTRO POR SERVIÇO
        if ($request->filled('service')) {
            $service = $request->input('service');
            $query->whereHas('servico', function($q) use ($service) {
                $q->where('SERVICO_CLINICA_DESC', 'like', "%{$service}%");
            });
        }

        // FILTRO POR VALOR
        if ($request->filled('valor')) {
            $valorFormatado = str_replace(',', '.', $request->input('valor'));
            $query->where('VALOR_AGEND', '=', $valorFormatado);
        }

        // FILTRO POR LOCAL
        if ($request->filled('local')) {
            $local = $request->input('local');
            $query->where('LOCAL', 'like', "%{$local}%");
        }

        $query->orderBy('DT_AGEND', 'desc');

        // Limita o número de registros retornados - Limite de 100
        $limit = min((int) $request->input('limit', 10), 100);

        $agendamentos = $query->limit($limit)->get();

        return response()->json($agendamentos);
    }

    // RETORNA AGENDAMENTOS PARA ALUNO
    public function getAgendamentosForAluno(Request $request)
    {
        $query = FaesaClinicaAgendamento::with([
            'paciente',
            'servico',
            'clinica',
            'agendamentoOriginal',
            'remarcacoes',
            'aluno'
        ])
        ->where('ID_CLINICA', 1)
        ->where('ID_ALUNO', session('aluno')[1])
        ->where('STATUS_AGEND', '<>', 'Excluido');

        // Filtro por nome ou CPF do paciente
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('paciente', function($q) use ($search) {
                $q->where('NOME_COMPL_PACIENTE', 'like', "%{$search}%")
                ->orWhere('CPF_PACIENTE', 'like', "%{$search}%");
            });
        }

        // FILTRO POR ALUNO
        if ($request->filled('aluno')) {
            $aluno = $request->input('aluno');

            $query->whereHas('aluno', function($q) use ($aluno) {
                $q->where('ALUNO', 'like', "{$aluno}%")
                ->orWhere('NOME_COMPL', 'like', "%{$aluno}%");
            });
        }

        // FILTRO POR DATA
        if ($request->filled('date')) {
            try {
                $date = Carbon::parse($request->input('date'))->format('Y-m-d');
                $query->where('DT_AGEND', $date);
            } catch (\Exception $e) {
                // DATA INVÁLIDA - IGNORA FILTRO
            }
        }

        // FILTRO POR HORA DE INÍCIO
        if ($request->filled('start_time')) {
            try {
                $startTime = Carbon::createFromFormat('H:i', $request->input('start_time'))->format('H:i:s');
                $query->where('HR_AGEND_INI', '>=', $startTime);
            } catch (\Exception $e) {
                // Hora inválida - ignora filtro
            }
        }

        // FILTRO POR HORA DE FIM
        if ($request->filled('end_time')) {
            try {
                $endTime = Carbon::createFromFormat('H:i', $request->input('end_time'))->format('H:i:s');
                $query->where('HR_AGEND_FIN', '<=', $endTime);
            } catch (\Exception $e) {
                // Hora inválida - ignora filtro
            }
        }

        // FILTRO POR STATUS
        if ($request->filled('status')) {
            $query->where('STATUS_AGEND', $request->input('status'));
        }

        // FILTRO POR SERVIÇO
        if ($request->filled('service')) {
            $service = $request->input('service');
            $query->whereHas('servico', function($q) use ($service) {
                $q->where('SERVICO_CLINICA_DESC', 'like', "%{$service}%");
            });
        }

        // FILTRO POR VALOR
        if ($request->filled('valor')) {
            $valorFormatado = str_replace(',', '.', $request->input('valor'));
            $query->where('VALOR_AGEND', '=', $valorFormatado);
        }

        // FILTRO POR LOCAL
        if ($request->filled('local')) {
            $local = $request->input('local');
            $query->where('LOCAL', 'like', "%{$local}%");
        }

        $query->orderBy('DT_AGEND', 'desc');

        // Limita o número de registros retornados - Limite de 100
        $limit = min((int) $request->input('limit', 10), 100);

        $agendamentos = $query->limit($limit)->get();

        return response()->json($agendamentos);
    }

    // RETORNA AGENDAMENTOS PARA PROFESSOR
    public function getAgendamentosForProfessor(Request $request)
    {
        $professor = session('professor');
        $turmas = array_column($professor[4], 'TURMA');
        $alunos = DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_MATRICULA as mat')
            ->join('FAESA_CLINICA_AGENDAMENTO as ag', 'ag.ID_ALUNO', 'mat.ALUNO')
            ->whereIn('mat.TURMA', $turmas)
            ->pluck('ag.ID_ALUNO');

        $query = FaesaClinicaAgendamento::with([
            'paciente',
            'servico',
            'clinica',
            'agendamentoOriginal',
            'remarcacoes',
            'aluno'
        ])
            ->where('ID_CLINICA', 1)
            ->where('STATUS_AGEND', '<>', 'Excluido')
            ->whereIn('ID_ALUNO', $alunos);

        // FILTRO POR NOME OU CPF DO PACIENTE
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('paciente', function ($q) use ($search) {
                $q->where('NOME_COMPL_PACIENTE', 'like', "%{$search}%")
                    ->orWhere('CPF_PACIENTE', 'like', "%{$search}%");
            });
        }

        // FILTRO POR aluno
        if ($request->filled('aluno')) {
            $aluno = $request->input('aluno');

            $query->whereHas('aluno', function ($q) use ($aluno) {
                $q->where('ID_ALUNO', 'like', "{$aluno}%")
                    ->orWhere('NOME_COMPL', 'like', "%{$aluno}%");
            });
        }

        // FILTRO POR DATA
        if ($request->filled('date')) {
            $rawDate = $request->input('date');
            if (strtotime($rawDate)) {
                $date = Carbon::parse($rawDate)->format('Y-m-d');
                $query->whereDate('DT_AGEND', $date);
            }
            // se for inválido, ignora
        }

        // FILTRO POR HORA DE INÍCIO
        if ($request->filled('start_time')) {
            $rawStart = $request->input('start_time');
            try {
                $startTime = Carbon::createFromFormat('H:i', $rawStart)->format('H:i:s');
                $query->where('HR_AGEND_INI', $startTime);
            } catch (\Exception $e) {
                // ignora se for inválido
            }
        }

        // FILTRO POR HORA DE FIM
        if ($request->filled('end_time')) {
            $rawEnd = $request->input('end_time');
            try {
                $endTime = Carbon::createFromFormat('H:i', $rawEnd)->format('H:i:s');
                $query->where('HR_AGEND_FIN', $endTime);
            } catch (\Exception $e) {
                // ignora se for inválido
            }
        }

        // FILTRO POR STATUS
        if ($request->filled('status')) {
            $query->where('STATUS_AGEND', $request->input('status'));
        }

        // FILTRO POR SERVIÇO
        if ($request->filled('service')) {
            $service = $request->input('service');
            $query->whereHas('servico', function ($q) use ($service) {
                $q->where('SERVICO_CLINICA_DESC', 'like', "%{$service}%");
            });
        }

        // FILTRO POR VALOR
        if ($request->filled('valor')) {
            $valorFormatado = str_replace(',', '.', $request->input('valor'));
            if (is_numeric($valorFormatado)) {
                $query->where('VALOR_AGEND', '=', $valorFormatado);
            }
        }

        // FILTRO POR LOCAL
        if ($request->filled('local')) {
            $local = $request->input('local');
            $query->where('LOCAL', 'like', "%{$local}%");
        }

        $query->orderBy('DT_AGEND', 'desc');

        // Limita o número de registros retornados - Limite de 100
        $limit = min((int)$request->input('limit', 10), 100);

        $agendamentos = $query->limit($limit)->get();

        return response()->json($agendamentos);
    }

    public function atualizarAgendamento(array $dados): FaesaClinicaAgendamento
    {
        $agendamentoOriginal = FaesaClinicaAgendamento::findOrFail($dados['ID_AGENDAMENTO']);

        // Formata o valor monetário
        if (!empty($dados['VALOR_AGEND'])) {
            $dados['VALOR_AGEND'] = str_replace(',', '.', $dados['VALOR_AGEND']);
        }

        $houveAlteracaoDeDataHora =
            $dados['DT_AGEND'] != $agendamentoOriginal->DT_AGEND->format('Y-m-d') ||
            $dados['HR_AGEND_INI'] != Carbon::parse($agendamentoOriginal->HR_AGEND_INI)->format('H:i');

        $dadosParaValidar = $this->_mapearDadosRequestParaValidacao($dados);

        // Valida disponibilidade sempre, ignorando o próprio agendamento
        $motivoFalha = $this->_validarDisponibilidade($dadosParaValidar, $agendamentoOriginal->ID_AGENDAMENTO);
        if ($motivoFalha !== null) {
            throw new \Exception($motivoFalha);
        }

        if ($houveAlteracaoDeDataHora) {
            // Se mudou data ou hora, cria remarcação
            $agendamentoOriginal->update(['STATUS_AGEND' => 'Remarcado']);
            $dadosParaCriar = $this->_mapearDadosRequestParaCriacao($dados, $agendamentoOriginal);
            return $this->_criarAgendamentoUnico($dadosParaCriar);
        } else {
            // Para mudanças simples, aplica as validações antes de atualizar
            $agendamentoOriginal->update($this->_mapearDadosRequestParaUpdate($dados));
            return $agendamentoOriginal;
        }
    }

    public function criarAgendamentos(array $dados): array
    {
        $datasParaAgendar = $this->_calcularDatasParaAgendar($dados);

        $agendamentosCriados = 0;
        $diasComErro = [];

        foreach ($datasParaAgendar as $data) {
            $dados['dia_agend'] = $data->format('Y-m-d');

            // Executa todas as validações para a data atual
            $motivoFalha = $this->_validarDisponibilidade($dados);

            if ($motivoFalha === null) {
                // Se não houve falha, cria o agendamento
                $this->_criarAgendamentoUnico($dados);
                $agendamentosCriados++;
            } else {
                // Se houve falha, armazena o motivo
                $diasComErro[$data->format('d/m/Y')] = $motivoFalha;
            }
        }

        return [
            'criados' => $agendamentosCriados,
            'erros' => $diasComErro,
        ];
    }

    private function _validarDisponibilidade(array $dados, ?int $idParaIgnorar = null): ?string
    {
        $data = $dados['dia_agend'];
        $hrIni = $dados['hr_ini'];
        $hrFim = $dados['hr_fim'];
        $idSala = $dados['id_sala_clinica'] ?? null;
        $idaluno = $dados['ID_ALUNO'] ?? null;
        $idPaciente = $dados['paciente_id'];

        if ($this->_isFeriado($data)) return "A data selecionada é um feriado.";
        if (!$this->_isHorarioDisponivel($data, $hrIni, $hrFim)) return "Horário bloqueado ou fora do período de atendimento.";
        if ($idSala) {
            if (!$this->_isSalaAtiva($idSala)) return "A sala selecionada está inativa.";
            if ($this->_hasConflito('ID_SALA', $idSala, $data, $hrIni, $hrFim, $idParaIgnorar)) return "Sala já ocupada neste horário.";
        }
        if ($idaluno) {
            if ($this->_hasConflito('ID_ALUNO', $idaluno, $data, $hrIni, $hrFim, $idParaIgnorar)) return "aluno indisponível neste horário.";
        }
        if ($this->_hasConflito('ID_PACIENTE', $idPaciente, $data, $hrIni, $hrFim, $idParaIgnorar)) return "Paciente já possui um agendamento neste horário.";

        return null;
    }

    private function _hasConflito(string $coluna, int $id, string $data, string $hrIni, string $hrFim, ?int $idParaIgnorar = null): bool
    {
        $query = FaesaClinicaAgendamento::where('ID_CLINICA', self::ID_CLINICA)
            ->where($coluna, $id)
            ->where('DT_AGEND', $data)
            ->whereNotIn('STATUS_AGEND', ['Excluido', 'Cancelado', 'Remarcado'])
            ->where(function ($query) use ($hrIni, $hrFim) {
                $query->where('HR_AGEND_INI', '<', $hrFim)
                      ->where('HR_AGEND_FIN', '>', $hrIni);
            });

        if ($idParaIgnorar) {
            $query->where('ID_AGENDamento', '<>', $idParaIgnorar);
        }

        return $query->exists();
    }

    private function _isFeriado(string $data): bool
    {
        $dataCarbon = Carbon::parse($data)->format('Y-m-d');
        return DB::table('LYCEUM_BKP_PRODUCAO.dbo.LY_FERIADO')
            ->where('DATA', '=', $dataCarbon)
            ->exists();
    }

    private function _isHorarioDisponivel(string $data, string $horaInicio, string $horaFim): bool
    {
        // Verifica se há algum bloqueio explícito que se sobreponha ao horário desejado
        $temBloqueio = FaesaClinicaHorario::where('ID_CLINICA', self::ID_CLINICA)
            ->where('BLOQUEADO', 'S')
            ->whereDate('DATA_HORARIO_INICIAL', '<=', $data)
            ->whereDate('DATA_HORARIO_FINAL', '>=', $data)
            ->whereTime('HR_HORARIO_INICIAL', '<', $horaFim)
            ->whereTime('HR_HORARIO_FINAL', '>', $horaInicio)
            ->exists();

        return !$temBloqueio; // Se tem bloqueio, não está disponível
    }

    private function _isSalaAtiva(int $idSala): bool
    {
        $sala = FaesaClinicaSala::find($idSala);
        return $sala && $sala->ATIVO === 'S'; // Supondo 'S' para Ativo
    }

    private function _mapearDadosRequestParaValidacao(array $dados): array
    {
        return [
            'dia_agend'       => $dados['DT_AGEND'],
            'hr_ini'          => $dados['HR_AGEND_INI'],
            'hr_fim'          => $dados['HR_AGEND_FIN'],
            'id_sala_clinica' => $dados['ID_SALA'] ?? null,
            'ID_ALUNO'    => $dados['ID_ALUNO'] ?? null,
            'paciente_id'     => $dados['ID_PACIENTE'],
        ];
    }

    private function _mapearDadosRequestParaCriacao(array $dados, FaesaClinicaAgendamento $original): array
    {
        return [
            'ID_CLINICA'         => $original->ID_CLINICA,
            'paciente_id'        => $dados['ID_PACIENTE'],
            'id_servico'         => $dados['ID_SERVICO'],
            'ID_ALUNO'       => $dados['ID_ALUNO'] ?? null,
            'id_sala_clinica'    => $dados['ID_SALA'] ?? null,
            'dia_agend'          => $dados['DT_AGEND'],
            'hr_ini'             => $dados['HR_AGEND_INI'],
            'hr_fim'             => $dados['HR_AGEND_FIN'],
            'status_agend'       => $dados['STATUS_AGEND'],
            'valor_agend'        => $dados['VALOR_AGEND'] ?? null,
            'observacoes'        => $dados['OBSERVACOES'] ?? null,
            'id_agend_remarcado' => $original->ID_AGENDAMENTO,
        ];
    }

    private function _mapearDadosRequestParaUpdate(array $dados): array
    {
        $local = !empty($dados['ID_SALA']) ? FaesaClinicaSala::find($dados['ID_SALA'])->DESCRICAO : null;
        return [
            'ID_SERVICO'   => $dados['ID_SERVICO'],
            'ID_PACIENTE' => $dados['ID_PACIENTE'],
            'ID_ALUNO' => $dados['ID_ALUNO'] ?? null,
            'ID_SALA'      => $dados['ID_SALA'] ?? null,
            'STATUS_AGEND' => $dados['STATUS_AGEND'],
            'VALOR_AGEND'  => $dados['VALOR_AGEND'] ?? null,
            'OBSERVACOES'  => $dados['OBSERVACOES'] ?? null,
            'LOCAL'        => $local,
        ];
    }

    private function _criarAgendamentoUnico(array $dados): FaesaClinicaAgendamento
    {
        $localAgend = null;
        if (!empty($dados['id_sala_clinica'])) {
            $sala = FaesaClinicaSala::find($dados['id_sala_clinica']);
            $localAgend = $sala ? $sala->DESCRICAO : null;
        }
        
        $novoAgendamento = FaesaClinicaAgendamento::create([
            'ID_CLINICA'         => $dados['ID_CLINICA'] ?? self::ID_CLINICA,
            'ID_PACIENTE'        => $dados['paciente_id'],
            'ID_USUARIO'         => $dados['usuario_id'],
            'ID_SERVICO'         => $dados['id_servico'],
            'ID_ALUNO'       => $dados['ID_ALUNO'] ?? null,
            'ID_SALA'            => $dados['id_sala_clinica'] ?? null,
            'DT_AGEND'           => $dados['dia_agend'],
            'HR_AGEND_INI'       => $dados['hr_ini'],
            'HR_AGEND_FIN'       => $dados['hr_fim'],
            'STATUS_AGEND'       => $dados['status_agend'] ?? 'Agendado',
            'VALOR_AGEND'        => $dados['valor_agend'] ?? null,
            'OBSERVACOES'        => $dados['observacoes'] ?? null,
            'LOCAL'              => $localAgend,
            'ID_AGEND_REMARCADO' => $dados['id_agend_remarcado'] ?? null,
        ]);

        return $novoAgendamento;
    }

    private function _calcularDatasParaAgendar(array $dados): array
    {
        $datasParaAgendar = [];
        $dataInicio = Carbon::parse($dados['dia_agend']);

        if (($dados['tem_recorrencia'] ?? '0') === '1') {
            $diasSemana = $dados['dias_semana'] ?? [];
            $dataFim = isset($dados['duracao_meses_recorrencia'])
                ? $dataInicio->copy()->addMonths((int) $dados['duracao_meses_recorrencia'])
                : (isset($dados['data_fim_recorrencia']) ? Carbon::parse($dados['data_fim_recorrencia']) : $dataInicio->copy()->addMonths(1));

            for ($data = $dataInicio->copy(); $data->lte($dataFim); $data->addDay()) {
                if (empty($diasSemana) || in_array($data->dayOfWeek, $diasSemana)) {
                    $datasParaAgendar[] = $data->copy();
                    if (empty($diasSemana)) break;
                }
            }
        } else {
            // Recorrência baseada no serviço
            $servico = FaesaClinicaServico::find($dados['id_servico']);
            if ($servico) {
                $dataFim = null;
                $servicoDesc = strtolower($servico->SERVICO_CLINICA_DESC);

                if (in_array($servicoDesc, ['triagem', 'plantão'])) {
                    $dataFim = $dataInicio->copy()->addWeeks(2);
                } elseif (in_array($servicoDesc, ['psicodiagnóstico','psicoterapia', 'educação'])) {
                    $dataFim = $dataInicio->copy()->addMonths(6);
                }
                // ADICIONADO: Lógica para recorrência personalizada via tabela de serviço
                elseif (isset($servico->TEMPO_RECORRENCIA_MESES) && $servico->TEMPO_RECORRENCIA_MESES > 0) {
                    $dataFim = $dataInicio->copy()->addMonths((int) $servico->TEMPO_RECORRENCIA_MESES);
                }

                if ($dataFim) {
                    for ($data = $dataInicio->copy(); $data->lte($dataFim); $data->addWeek()) {
                        $datasParaAgendar[] = $data->copy();
                    }
                }
            }
        }

        if (empty($datasParaAgendar)) {
            $datasParaAgendar[] = $dataInicio;
        }

        return $datasParaAgendar;
    }

    public function criarAgendamentoAluno(Request $request)
    {
        dd($request);
    }

    // ADICIONA MENSAGEM DE MOTIVO DE CANCELAMENTO AO AGENDAMENTO
    public function addMensagemCancelamento($id, string $msg, $checkPagamento = null, $valorPagamento = null)
    {
        $agendamento = FaesaClinicaAgendamento::findOrFail($id);

        // Atualiza status e mensagem de cancelamento
        $agendamento->STATUS_AGEND = "Cancelado";
        $agendamento->MENSAGEM = $msg;

        // Atualiza status de pagamento e valor, se enviados
        if (!is_null($checkPagamento)) {
            $agendamento->STATUS_PAG = $checkPagamento;
        }

        if (!is_null($valorPagamento)) {
            $agendamento->VALOR_PAG = $valorPagamento;
        }

        $agendamento->save();

        return $agendamento;
    }
}