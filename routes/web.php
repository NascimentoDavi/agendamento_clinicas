<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Psicologia\PacienteController;
use App\Http\Controllers\Psicologia\AgendamentoController;
use App\Http\Controllers\Psicologia\ServicoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Psicologia\AlunoController;
use App\Http\Controllers\Psicologia\ClinicaController;
use App\Http\Controllers\Psicologia\SalaController;
use App\Http\Controllers\Psicologia\HorarioController;
use App\Http\Controllers\Psicologia\DisciplinaController;
use App\Models\FaesaClinicaServico;
use App\Models\FaesaClinicaPaciente;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\AuthProfessorMiddleware;
use App\Http\Middleware\CheckClinicaMiddleware;
use App\Http\Middleware\AuthAlunoMiddleware;

/*
|--------------------------------------------------------------------------
| ROTAS DE AUTENTICAÇÃO GERAL
|--------------------------------------------------------------------------
*/
Route::middleware([AuthMiddleware::class])->group(function () {

    Route::get('/', function () {
        return redirect()->route('loginGET');
    })->name('loginRoot');

});


/*
|--------------------------------------------------------------------------
| ROTAS DE ADMINISTRADOR
|--------------------------------------------------------------------------
*/
Route::middleware([AuthMiddleware::class])
    ->prefix('psicologia')
    ->group(function () {

        Route::get('/login', function () {
            if (session()->has('usuario')) {
                return redirect()->route('menu_agenda_psicologia');
            }
            return view('login');
        })->name('loginGET');

        Route::post('/login', [LoginController::class, 'login'])->name('loginPOST');

        Route::get('/logout', function () {
            session()->forget('usuario');
            return redirect()->route('loginGET');
        })->name('logout');

        //----- ROTAS GERAIS E RELATÓRIOS -----//
        Route::get('/', function () {
            $usuario = session('usuario');
            return view('psicologia.adm/menu_agenda', compact('usuario'));
        })->name('menu_agenda_psicologia');

        Route::get('/relatorios-agendamento', function () {
            return view('psicologia.adm/relatorios_agendamento');
        })->name('relatorio_psicologia');

        //----- PACIENTES -----//
        Route::get('/criar-paciente', function () {
            return view('psicologia.adm/criar_paciente');
        })->name('criarpaciente_psicologia');
        Route::post('/criar-paciente/criar', [PacienteController::class, 'createPaciente'])->name('criarPaciente-Psicologia');
        Route::get('/editar-paciente', function () {
            return view('psicologia.adm/editar_paciente');
        })->name('editarPacienteView-Psicologia');
        Route::post('/editar-paciente/{id}', [PacienteController::class, 'editarPaciente'])->name('editarPaciente-Psicologia');
        Route::get('/consultar-paciente', function () {
            return view('psicologia.adm.consultar_paciente');
        })->name('consultar-paciente');
        Route::get('/consultar-paciente/buscar', [PacienteController::class, 'getPaciente'])->name('getPaciente');
        Route::get('/consultar-paciente/buscar-nome-cpf', [PacienteController::class, 'getPacienteByNameCpf'])->name('getPacienteByNameCpf');
        Route::get('/paciente/{id}/ativar', [PacienteController::class, 'setAtivo'])->name('ativarPaciente-Psicologia');
        Route::delete('/excluir-paciente/{id}', [PacienteController::class, 'deletePaciente'])->name('deletePaciente-Psicologia');
        Route::get('/api/buscar-pacientes', function () {
            $query = request()->input('query', '');
            $pacientes = FaesaClinicaPaciente::where(function ($q) use ($query) {
                $q->where('NOME_COMPL_PACIENTE', 'like', "%{$query}%")
                    ->orWhere('CPF_PACIENTE', 'like', "%{$query}%");
            })
                ->limit(10)
                ->get(['ID_PACIENTE', 'NOME_COMPL_PACIENTE', 'CPF_PACIENTE']);
            return response()->json($pacientes);
        });

        //----- AGENDAMENTOS -----//
        Route::get('/criar-agendamento', function () {
            return view('psicologia.adm/criar_agenda');
        })->name('criaragenda_psicologia');
        Route::post('/criar-agendamento/criar', [AgendamentoController::class, 'criarAgendamento'])->name('criarAgendamento-Psicologia');
        Route::get('/consultar-agendamento', function () {
            return view('psicologia.adm.consultar_agendamento');
        })->name('listagem-agendamentos');
        Route::post('/consultar-agendamento/consultar', [AgendamentoController::class, 'getAgendamento'])->name('getAgendamento');
        Route::get('/get-agendamento', [AgendamentoController::class, 'getAgendamento']);
        Route::get('/agendamentos-calendar/adm', [AgendamentoController::class, 'getAgendamentosForCalendar']);
        Route::get('/agendamentos/paciente/{id}', [AgendamentoController::class, 'getAgendamentosByPaciente']);
        Route::get('/agendamento/{id}', [AgendamentoController::class, 'showAgendamento'])->name('agendamento.show');
        Route::get('/agendamento/{id}/editar', [AgendamentoController::class, 'editAgendamento'])->name('agendamento.edit');
        Route::put('/agendamento', [AgendamentoController::class, 'updateAgendamento'])->name('agendamento.update');
        Route::put('/agendamentos/{id}/status', [AgendamentoController::class, 'atualizarStatus']);
        Route::put('/agendamentos/{id}/mensagem-cancelamento', [AgendamentoController::class, 'addMensagemCancelamento']);
        Route::delete('/agendamento/{id}', [AgendamentoController::class, 'deleteAgendamento'])->name('psicologia.agendamento.delete');

        //----- SERVIÇOS -----//
        Route::get('/criar-servico', function () {
            return view('psicologia.adm/criar_servico');
        })->name('criarservico_psicologia');
        Route::get('/servicos', [ServicoController::class, 'getServicos']);
        Route::get('/servicos/{id}', [ServicoController::class, 'getServicoById']);
        Route::post('/servicos', [ServicoController::class, 'criarServico']);
        Route::post('/criar-servico/criar', [ServicoController::class, 'criarServico'])->name('criarServico-Psicologia');
        Route::put('/servicos/{id}', [ServicoController::class, 'atualizarServico']);
        Route::delete('/servicos/{id}', [ServicoController::class, 'deletarServico']);
        Route::get('/pesquisar-servico', [ServicoController::class, 'getServicos'])->name('pesquisarServico-Psicologia');
        Route::get('/api/buscar-servicos', function () {
            $query = request()->input('query', '');
            $servicos = FaesaClinicaServico::where('SERVICO_CLINICA_DESC', 'like', "%{$query}%")
                ->where('ID_CLINICA', 1)
                ->limit(10)
                ->get(['ID_SERVICO_CLINICA', 'SERVICO_CLINICA_DESC']);
            return response()->json($servicos);
        });

        //----- SALAS -----//
        Route::get('/criar-sala', function () {
            return view('psicologia.adm.criar_sala');
        })->name('salas_psicologia');
        Route::get('/salas/listar', [SalaController::class, 'listSalas'])->name('listarSalas-Psicologia');
        Route::post('/salas/criar', [SalaController::class, 'createSala'])->name('criarSala-Psicologia');
        Route::put('/salas/{id}', [SalaController::class, 'updateSala'])->name('atualizarSala-Psicologia');
        Route::delete('salas/{id}', [SalaController::class, 'deleteSala'])->name('deleteSala-Psicologia');
        Route::get('/pesquisar-local', [SalaController::class, 'getSala'])->name('pesquisarLocal-Psicologia');

        //----- HORÁRIOS -----//
        Route::get('/criar-horario', function () {
            return view('psicologia.adm.criar_horario');
        })->name('criarHorarioView-Psicologia');
        Route::get('/horarios/listar', [HorarioController::class, 'listHorarios'])->name('listarHorarios-Psicologia');
        Route::post('/horarios/criar-horario', [HorarioController::class, 'createHorario'])->name('criarHorario-Psicologia');
        Route::put('/horarios/atualizar/{id}', [HorarioController::class, 'updateHorario'])->name('updateHorario-Psicologia');
        Route::delete('/horarios/deletar/{id}', [HorarioController::class, 'deleteHorario'])->name('deleteHorario-Psicologia');

        //----- DISCIPLINAS E ALUNOS (Contexto Admin) -----//
        Route::get('/disciplinas-psicologia', [DisciplinaController::class, 'getDisciplina']);
        Route::get('/disciplina/{codigo}', [DisciplinaController::class, 'getDisciplinaByCodigo'])->name('getDisciplinaByCodigo');
        Route::get('/listar-alunos', [AlunoController::class, 'listAlunos']);
        Route::get('buscar-aluno/{matricula}', [AlunoController::class, 'listAlunos'])->name('listAlunos-Psicologia');
    });


/*
|--------------------------------------------------------------------------
| ROTAS DA ÁREA DO ALUNO
|--------------------------------------------------------------------------
*/
Route::middleware([AuthMiddleware::class])
    ->prefix('aluno')
    ->group(function () {

    //----- AUTENTICAÇÃO E MENU -----//
    Route::get('/login', function () {
        return session()->has('aluno')
            ? redirect()->route('alunoAgenda')
            : view('psicologia.aluno.login_aluno');
    })->name('alunoLoginGet');
    Route::post('/login', function () {
        return redirect()->route('alunoAgenda');
    })->name('alunoLoginPost');
    Route::get('/', function () {
        if (session()->has('aluno')) {
            return view('psicologia.aluno.menu_agenda');
        }
    })->name('alunoAgenda');
    Route::get('/logout', function () {
        session()->forget('aluno');
        return redirect()->route('alunoLoginGet');
    })->name('alunoLogout');

    //----- AGENDAMENTOS (Visão do Aluno) -----//
    Route::get('/agendamentos-calendar', [AgendamentoController::class, 'getAgendamentosForCalendaraluno']);
    Route::get('/consultar-agendamento', function () {
        return view('psicologia.aluno.consultar_agenda');
    })->name('alunoConsultarAgendamentos-GET');
    Route::get('/consultar-agendamento/buscar', [AgendamentoController::class, 'getAgendamentosForAluno'])->name('getAgendamentosForAluno');
    Route::get('/criar-agendamento', function () {
        return view('psicologia.aluno.criar_agenda');
    })->name('alunoCriarAgenda-Get');
    Route::post('/criar-agendamento/criar', [AgendamentoController::class, 'criarAgendamentoaluno'])->name('criarAgendamento-aluno');
    Route::get('/agendamento/{id}/editar', [AgendamentoController::class, 'editAgendamentoaluno'])->name('agendamentoaluno.edit');
    Route::put('/agendamento', [AgendamentoController::class, 'updateAgendamento'])->name('aluno.agendamento.update');
    Route::delete('/agendamento/{id}', [AgendamentoController::class, 'AlunoDeleteAgendamento'])->name('aluno.agendamento.delete');

    //----- CONSULTAS (Visão do Aluno) -----//
    Route::get('/consultar-paciente/buscar', [PacienteController::class, 'getPacienteByNameCPFaluno'])->name('alunoGetPaciente');
    Route::get('/pesquisar-disciplina', [ServicoController::class, 'getDisciplinaServico'])->name('alunoGetDisciplina');
});


/*
|--------------------------------------------------------------------------
| ROTAS DA ÁREA DO PROFESSOR
|--------------------------------------------------------------------------
*/
Route::middleware([AuthMiddleware::class])
    ->prefix('professor')
    ->group(function () {

    //----- AUTENTICAÇÃO E MENU -----//
    Route::get('/login', function () {
        return session()->has('professor')
            ? view('psicologia.professor.menu_agenda')
            : view('psicologia.professor.login_professor');
    })->name('professorLoginGet');
    Route::post('/login', function () {
        if (session()->has('professor')) {
            return view('psicologia.professor.menu_agenda');
        }
    })->name('professorLoginPost');
    Route::match(['get', 'post'], '/', function () {
        return view('psicologia.professor.menu_agenda');
    })->name('professorMenu');
    Route::get('/logout', function () {
        session()->forget('professor');
        return redirect()->route('professorLoginGet');
    })->name('professorLogout');


    //----- CONSULTAS (Visão do Professor) -----//
    Route::get('/agendamentos-calendar', [AgendamentoController::class, 'getAgendamentosForCalendarProfessor'])->name('getAgendamentosForCalendarProfessor');
    Route::get('/consultar-agendamento', function () {
        return view('psicologia.professor.consultar_agenda');
    })->name('professorConsultarAgendamentos-GET');
    Route::get('/consultar-agendamento/buscar', [AgendamentoController::class, 'getAgendamentosForProfessor'])->name('getAgendamentosForProfessor');
    Route::get('/aluno', function () {
        return view('psicologia.professor.consultar_aluno');
    });
});