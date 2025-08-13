<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Odonto\OdontoCreateController;
use App\Http\Controllers\Odonto\OdontoConsultController;
use App\Http\Controllers\Odonto\OdontoUpdateController;
use App\Http\Controllers\Psicologia\PacienteController;
use App\Http\Controllers\Psicologia\AgendamentoController;
use App\Http\Controllers\Psicologia\ServicoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Psicologia\ClinicaController;
use App\Http\Controllers\Psicologia\SalaController;
use App\Http\Controllers\Psicologia\HorarioController;
use App\Models\FaesaClinicaServico;
use App\Models\FaesaClinicaPaciente;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\CheckClinicaMiddleware;


// -------------------- ODONTOLOGIA --------------------

// MENU
Route::get('/odontologia/menu_agenda', function () {
    $usuario = session('usuario');
    return view('odontologia/menu_agenda', compact('usuario'));
})->name('menu_agenda');

// MIDDLEWARE DE ROTAS ODONTOLOGIA
Route::middleware([AuthMiddleware::class, CheckClinicaMiddleware::class])->prefix('odontologia')->group(function () {

    Route::get('/', function () {
        $usuario = session('usuario');
        return view('odontologia/menu_agenda', compact('usuario'));
    })->name('menu_agenda_odontologia');

    Route::get('/relatorio', function () {
        return view('odontologia/report_agenda');
    })->name('relatorio_odontologia');

    Route::get('/criarpaciente', function () {
        return view('odontologia/create_patient');
    })->name('criarpaciente_odontologia');

    Route::get('/criaragenda', function () {
        return view('odontologia/create_agenda');
    })->name('criaragenda_odontologia');

    Route::get('/criarservico', function () {
        return view('odontologia/create_service');
    })->name('criarservico_odontologia');

    Route::get('/criarbox', function () {
        return view('odontologia/create_box');
    })->name('criarbox_odontologia');

    Route::get('/criarboxdisciplina', function () {
        return view('odontologia/create_box_discipline');
    })->name('criarbox_disciplina_odontologia');
});

// CRIAÇÃO E EDIÇÃO - PACIENTE
Route::get('/odontologia/criarpaciente', [OdontoCreateController::class, 'showForm'])->name('criarpaciente');
Route::get('/odontologia/criarpaciente/{pacienteId}', [OdontoCreateController::class, 'editPatient'])->name('editPatient');
Route::post('/odontologia/criarpaciente', [OdontoCreateController::class, 'fCreatePatient'])->name('createPatient');
Route::put('/updatePatient/{id}', [OdontoUpdateController::class, 'updatePatient'])->name('updatePatient');

// SERVIÇOS
Route::get('/odontologia/criarservico', function () {
    return view('odontologia/create_service');
})->name('criarservico');
Route::post('/odontologia/criarservico', [OdontoCreateController::class, 'createService'])->name('createService');
Route::get('/criarservico/{idService}', [OdontoCreateController::class, 'editService'])->name('editService');
Route::put('/criarservico/{idService}', [OdontoUpdateController::class, 'updateService'])->name('updateService');

// BOXES
Route::get('/odontologia/criarbox', function () {
    return view('odontologia/create_box');
})->name('criarbox');
Route::post('/odontologia/criarbox', [OdontoCreateController::class, 'createBox'])->name('createBox');
Route::get('odontologia/criarbox/{boxId}', [OdontoCreateController::class, 'editBox'])->name('editBox');
Route::put('/criarbox/{boxId}', [OdontoUpdateController::class, 'updateBox'])->name('updateBox');

// BOX-DISCIPLINAS
Route::post('/odontologia/criarboxdisciplina', [OdontoCreateController::class, 'createBoxDiscipline'])->name('createBoxDiscipline');
Route::get('/odontologia/criarboxdisciplina/{idBoxDiscipline}', [OdontoCreateController::class, 'editBoxDiscipline'])->name('editBoxDiscipline');
Route::put('/criarboxdisciplina/{idBoxDiscipline}', [OdontoUpdateController::class, 'updateBoxDiscipline'])->name('updateBoxDiscipline');

// AGENDA
Route::post('/odontologia/criaragenda', [OdontoCreateController::class, 'fCreateAgenda'])->name('createAgenda');
Route::get('/odontologia/criaragenda/{agendaId}', [OdontoCreateController::class, 'editAgenda'])->name('editAgenda');
Route::put('/updateAgenda/{id}', [OdontoUpdateController::class, 'updateAgenda'])->name('updateAgenda');

// CONSULTAS
Route::get('/odontologia/agendamentos', [OdontoConsultController::class, 'getAgendamentos']);
Route::get('/odontologia/disciplinas', [OdontoConsultController::class, 'getDisciplinas']);
Route::get('/odontologia/boxes', [OdontoConsultController::class, 'getBoxes']);
Route::get('/odontologia/boxeservicos/{servicoId}', [OdontoConsultController::class, 'getBoxeServicos']);
Route::get('/getPacientes', [OdontoConsultController::class, 'buscarPacientes']);
Route::get('/getServices', [OdontoConsultController::class, 'buscarServicos']);
Route::get('/getAgenda', [OdontoConsultController::class, 'buscarAgendamentos']);
Route::get('/getBoxes', [OdontoConsultController::class, 'buscarBoxes']);
Route::get('/getBoxDisciplines', [OdontoConsultController::class, 'buscarBoxeDisciplinas']);
Route::get('/getBoxDisciplines/{discipline}', [OdontoConsultController::class, 'boxesDisciplina']);
Route::get('/paciente/{pacienteId}', [OdontoConsultController::class, 'listaPacienteId']);
Route::get('/servicos/{servicoId}', [OdontoConsultController::class, 'listaServicosId']);
Route::get('/servicos', [OdontoConsultController::class, 'services']);
Route::get('/agenda/{pacienteId}', [OdontoConsultController::class, 'listaAgendamentoId']);

// CONSULTA DE VIEWS
Route::get('/odontologia/consultarpaciente', [OdontoConsultController::class, 'fSelectPatient'])->name('selectPatient');
Route::get('/odontologia/consultarservico', [OdontoConsultController::class, 'fSelectService'])->name('selectService');
Route::get('/odontologia/consultarbox', [OdontoConsultController::class, 'fSelectBox'])->name('selectBox');
Route::get('/odontologia/consultardisciplinabox', [OdontoConsultController::class, 'fSelectBoxDiscipline'])->name('selectBoxDiscipline');
Route::get('/odontologia/consultaragenda', [OdontoConsultController::class, 'fSelectAgenda'])->name('selectAgenda');

// EDIÇÕES MISC
Route::post('/alterarstatus/{agendaId}', [OdontoUpdateController::class, 'editStatus'])->name('editStatus');
Route::post('/definelocalatendimento/{agendaId,boxId}', [OdontoCreateController::class, 'defineLocalAtendimento'])->name('defineLocalAtendimento');

// VIEWS SEM CONTROLLER
Route::get('/consultarpaciente', function () {
    return view('odontologia/consult_patient');
})->name('consultarpaciente');
Route::get('/consultarservico', function () {
    return view('odontologia/consult_servico');
})->name('consultarservico');
Route::get('/consultarbox', function () {
    return view('odontologia/consult_box');
})->name('consultarbox');
Route::get('/consultardisciplinabox', function () {
    return view('odontologia/consult_box_discipline');
})->name('consultardisciplinabox');

// -----------------------------------------------------

// PÁGINA DE LOGIN - SELEÇÃO DE PSICOLOGIA OU ODONTOLOGIA
Route::get('/', function () {
    if (session()->has('usuario')) {
        return view('login');
    }

    // $usuario = session('usuario');
    // session(['last_clinic_route' => 'menu_agenda_psicologia']);
    return view('login', compact('usuario'));
})->name('menu_agenda_psicologia');

Route::get('/', function () {
    if (session()->has('usuario')) {
        $usuario = session('usuario');
        $clinicas = $usuario->pluck('ID_CLINICA')->toArray();
        $sit_usuario = session('SIT_USUARIO');

        if (in_array(1, $clinicas) && in_array(2, $clinicas)) {
            // SESSÃO AINDA EXISTE - TEM ACESSO ÀS DUAS CLÍNICAS
            $lastRoute = session('last_clinic_route');

            if ($lastRoute) {
                return redirect()->route($lastRoute);
            } else {
                // ABRE TELA DE SELEÇÃO - Se não tem LastRoute gravado, abre tela para seleção de clínica que deseja acessar
                return redirect()->route('selecionar-clinica-get');
            }
        } elseif (in_array(1, $clinicas)) {
            return redirect()->route('menu_agenda_psicologia');
        } elseif (in_array(2, $clinicas)) {
            return redirect()->route('menu_agenda_odontologia');
        } else {
            session()->flush();
            return redirect()->route('loginGET')->with('error', 'Usuário sem acesso a clínicas.');
        }
    }
    return view('login');
})->name('loginGET');

Route::middleware([AuthMiddleware::class])->group(function () {

    Route::get('/login', function () {
        if (session()->has('usuario')) {
            return redirect()->route('menu_agenda_psicologia');
        }
        return view('login');
    })->name('loginGET');

    Route::post('/login', [LoginController::class, 'login'])->name('loginPOST');

    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/selecionar-clinica', function () {
        return view('selecionar_clinica');
    })->name('selecionar-clinica-get');

    Route::post('/selecionar-clinica', [ClinicaController::class, 'selecionarClinica'])->name('selecionar-clinica-post');
});

Route::middleware([AuthMiddleware::class, CheckClinicaMiddleware::class])
    ->prefix('psicologia')
    ->group(function () {

    // ROOT
    Route::get('/', function () {
        $usuario = session('usuario');
        return view('psicologia/menu_agenda', compact('usuario'));
    })->name('menu_agenda_psicologia');

    // RELATÓRIOS
    Route::get('/relatorios-agendamento', function () {
        return view('psicologia/relatorios_agendamento');
    })->name('relatorio_psicologia');

    // CRIAR PACIENTE
    Route::get('/criar-paciente', function () {
        return view('psicologia/criar_paciente');
    })->name('criarpaciente_psicologia');
    Route::post('/criar-paciente/criar', [PacienteController::class, 'createPaciente'])->name('criarPaciente-Psicologia');

    // EDITAR PACIENTE
    Route::get('/editar-paciente', function () {
        return view('psicologia/editar_paciente');
    })->name('editarPacienteView-Psicologia');
    Route::post('/editar-paciente/{id}', [PacienteController::class, 'editarPaciente'])->name('editarPaciente-Psicologia');

    // API BUSCAR PACIENTES
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

    // CONSULTAR PACIENTE
    Route::get('/consultar-paciente', function () {
        return view('psicologia.consultar_paciente');
    })->name('consultar-paciente');
    Route::get('/consultar-paciente/buscar', [PacienteController::class, 'getPaciente'])->name('getPaciente');
    Route::get('/paciente/{id}/ativar', [PacienteController::class, 'setAtivo'])->name('ativarPaciente-Psicologia');
    Route::delete('/excluir-paciente/{id}', [PacienteController::class, 'deletePaciente'])->name('deletePaciente-Psicologia');

    // AGENDAMENTOS
    Route::get('/criar-agendamento', function () {
        return view('psicologia/criar_agenda');
    })->name('criaragenda_psicologia');
    Route::post('/criar-agendamento/criar', [AgendamentoController::class, 'criarAgendamento'])->name('criarAgendamento-Psicologia');
    Route::put('/agendamentos/{id}/status', [AgendamentoController::class, 'atualizarStatus']);
    Route::put('/agendamentos/{id}/mensagem-cancelamento', [AgendamentoController::class, 'addMensagemCancelamento']);
    Route::get('/consultar-agendamento', function () {
        return view('psicologia.consultar_agendamento');
    })->name('listagem-agendamentos');
    Route::post('/consultar-agendamento/consultar', [AgendamentoController::class, 'getAgendamento'])->name('getAgendamento');
    Route::get('/get-agendamento', [AgendamentoController::class, 'getAgendamento']);
    Route::get('/agendamentos/paciente/{id}', [AgendamentoController::class, 'getAgendamentosByPaciente']);
    Route::get('/agendamento/{id}', [AgendamentoController::class, 'showAgendamento'])->name('agendamento.show');
    Route::get('/agendamentos-calendar', [AgendamentoController::class, 'getAgendamentosForCalendar']);
    Route::get('/agendamento/{id}/editar', [AgendamentoController::class, 'editAgendamento'])->name('agendamento.edit');
    Route::put('/agendamento/{id}', [AgendamentoController::class, 'updateAgendamento'])->name('agendamento.update');
    Route::delete('/agendamento/{id}', [AgendamentoController::class, 'deleteAgendamento'])->name('psicologia.agendamento.delete');

    // SERVIÇOS
    Route::get('/criar-servico', function () {
        return view('psicologia/criar_servico');
    })->name('criarservico_psicologia');
    Route::post('/criar-servico/criar', [ServicoController::class, 'criarServico'])->name('criarServico-Psicologia');
    Route::get('/pesquisar-servico', [ServicoController::class, 'getServicos'])->name('pesquisarServico-Psicologia');
    Route::get('/api/buscar-servicos', function () {
        $query = request()->input('query', '');
        $servicos = FaesaClinicaServico::where('SERVICO_CLINICA_DESC', 'like', "%{$query}%")
            ->where('ID_CLINICA', 1)
            ->limit(10)
            ->get(['ID_SERVICO_CLINICA', 'SERVICO_CLINICA_DESC']);

        return response()->json($servicos);
    });
    Route::get('/servicos', [ServicoController::class, 'getServicos']);
    Route::get('/servicos/{id}', [ServicoController::class, 'getServicoById']);
    Route::post('/servicos', [ServicoController::class, 'criarServico']);
    Route::put('/servicos/{id}', [ServicoController::class, 'atualizarServico']);
    Route::delete('/servicos/{id}', [ServicoController::class, 'deletarServico']);

    // SALAS
    Route::get('/criar-sala', function () {
        return view('psicologia.criar_sala');
    })->name('salas_psicologia');
    Route::post('/salas/criar', [SalaController::class, 'createSala'])->name('criarSala-Psicologia');
    Route::get('/salas/listar', [SalaController::class, 'listSalas'])->name('listarSalas-Psicologia');
    Route::put('/salas/{id}', [SalaController::class, 'updateSala'])->name('atualizarSala-Psicologia');
    Route::get('/pesquisar-local', [SalaController::class, 'getSala'])->name('pesquisarLocal-Psicologia');

    // HORÁRIOS
    Route::get('/criar-horario', function () {
        return view('psicologia.criar_horario');
    })->name('criarHorarioView-Psicologia');
    Route::post('/horarios/criar-horario', [HorarioController::class, 'createHorario'])->name('criarHorario-Psicologia');
    Route::get('/horarios/listar', [HorarioController::class, 'listHorarios'])->name('listarHorarios-Psicologia');
    Route::put('/horarios/atualizar/{id}', [HorarioController::class, 'updateHorario'])->name('updateHorario-Psicologia');
    Route::delete('/horarios/deletar/{id}', [HorarioController::class, 'deleteHorario'])->name('deleteHorario-Psicologia');
});
