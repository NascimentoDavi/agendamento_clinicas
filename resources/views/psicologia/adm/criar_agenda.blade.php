@extends('layouts.app_adm')

@section('title', 'Criar Agendamento')

@section('styles')
    <!-- FULL CALENDAR  -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />

    <!-- FONTES -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />

    @vite(['resources/css/page-title-header/app.css'])
@endsection

@section('content')

@if ($errors->any())
    <div id="alert-error" class="alert alert-danger alert-dismissible fade show shadow text-center position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050; max-width: 90%;" role="alert">
        <strong>Ops!</strong> Corrija os itens abaixo:
        <ul class="mb-0 mt-1 list-unstyled">
            @foreach ($errors->all() as $error)
                <li><i class="bi bi-exclamation-circle-fill me-1"></i> {{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif

@if(session('success'))
    <div id="alert-success" class="alert alert-success alert-dismissible fade show text-center shadow position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050;" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif

@if(session('error'))
    <div id="alert-error" class="alert alert-danger alert-dismissible fade show text-center shadow position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050; max-width: 90%;" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
@endif

<div class="mx-3 mb-2 mw-100">

    <div class="row">
        
        <x-page-title>
        </x-page-title>
        
        <div class="col-12 shadow-lg shadow-dark p-4 bg-body-tertiary rounded">

            <form action="{{ route('criarAgendamento-Psicologia') }}" method="POST" id="agendamento-form" class="w-100" validate>
                @csrf

                <input type="hidden" name="paciente_id" id="paciente_id" value="{{ old('paciente_id') }}"/>
                <input type="hidden" name="id_servico" id="id_servico" value="{{ old('id_servico') }}" />
                <input type="hidden" name="recorrencia" id="recorrencia"/>
                <input type="hidden" name="status_agend" value="Em aberto"/>
                <input type="hidden" name="usuario_id" value="{{ session('usuario')->ID_USUARIO_CLINICA }}">

                <div class="mb-3 position-relative">
                    <label for="select-paciente" class="form-label">Paciente</label>
                    <select id="select-paciente" name="paciente_id" placeholder="Pesquisar paciente por nome ou CPF..." autocomplete="off" data-old-id="{{ old('paciente_id') }}"></select>
                </div>

                <div class="mb-2">
                    <h5 class="mb-0">Horário e Detalhes</h5>
                    <hr class="mt-1">
                </div>

                <div class="row g-3">

                    <div class="col-12 mb-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" value="1" id="temRecorrencia" name="tem_recorrencia">
                            <label class="form-check-label fw-semibold" for="temRecorrencia">
                                <i class="bi bi-arrow-repeat me-1 text-primary"></i> Ativar recorrência
                            </label>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3 position-relative">
                        <label for="servico" class="form-label">
                            Serviço
                            <span id="info-observacao" style="display: none;">
                                <i class="bi bi-info-circle-fill"></i>
                            </span>
                        </label>
                        <select id="select-servico" name="id_servico" placeholder="Serviço do Atendimento..." autocomplete="off" data-old-id="{{ old('id_servico') }}" ></select>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <label for="data" class="form-label">Dia</label>
                        <input type="text" id="data" name="dia_agend" class="form-control" value="{{ old('dia_agend') }}">
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <label for="hr_ini" class="form-label">Horário Início</label>
                        <input type="text" id="hr_ini" name="hr_ini" class="form-control" value="{{ old('hr_ini') }}">
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <label for="hr_fim" class="form-label">Horário Fim</label>
                        <input type="text" id="hr_fim" name="hr_fim" class="form-control" value="{{ old('hr_fim') }}">
                    </div>

                    <div id="recorrenciaCampos" class="col-12 mt-2 d-none">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6 class="card-title text-primary mb-3">
                                    <i class="bi bi-calendar-week me-1"></i> Configuração de Recorrência
                                </h6>
                                <div class="d-flex flex-wrap gap-3 align-items-center justify-content-around">
                                    <div class="flex-grow-1">
                                        <label class="form-label">Dias da Semana</label>
                                        <div id="diasSemanaBtns" class="d-flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-dia="0">Dom</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-dia="1">Seg</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-dia="2">Ter</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-dia="3">Qua</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-dia="4">Qui</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-dia="5">Sex</button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-dia="6">Sáb</button>
                                        </div>
                                        <small class="text-muted">Clique nos dias desejados para selecionar.</small>
                                    </div>
                                    <div id="duracaoMesesContainer" style="min-width: 200px;">
                                        <label for="duracao_meses_recorrencia" class="form-label">Duração (meses)</label>
                                        <select id="duracao_meses_recorrencia" name="duracao_meses_recorrencia" class="form-select form-select-sm">
                                            <option value="" selected>Selecione</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">{{ $i }} mes{{ $i > 1 ? 'es' : '' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div style="min-width: 200px;">
                                        <label for="data_fim_recorrencia" class="form-label">Data Fim</label>
                                        <input type="text" id="data_fim_recorrencia" name="data_fim_recorrencia" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-sm-6 col-md-3">
                        <label for="valor_agend" class="form-label">Valor</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" name="valor_agend" id="valor_agend" class="form-control" placeholder="0,00" value="{{ old('valor_agend') }}">
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3 position-relative">
                        <input type="hidden" name="id_sala_clinica" id="id_sala_clinica">
                        <label for="select-local" class="form-label">Local</label>
                        <select id="select-local" name="id_sala_clinica" placeholder="Local do atendimento..." autocomplete="off" data-old-id="{{ old('id_sala_clinica') }}"></select>
                    </div>

                    <div class="col-md-6 position-relative">
                        <input type="hidden" name="ID_ALUNO" id="ID_ALUNO">
                        <label for="select-aluno" class="form-label">Aluno</label>
                        <select id="select-aluno" name="ID_ALUNO" placeholder="Aluno do Atendimento..." autocomplete="off" data-old-id="{{ old('ID_ALUNO') }}"></select>
                        <small id="aluno-count" class="text-muted mt-1 d-block"></small>
                    </div>
                              
                    <div class="col-12">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea name="observacoes" id="observacoes" class="form-control" placeholder="Observações..." rows="3">{{ old('observacoes') }}</textarea>
                    </div>

                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle-fill me-1"></i> Agendar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite(['resources/js/criar-agenda/app.js'])
@endsection
