@extends('layouts.app_adm')

@section('title', 'Consultar Agendamentos')

@section('styles')
    <!-- FULL CALENDAR  -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />

    <!-- FONTES -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />

    @vite(['resources/css/page-title-header/app.css'])
    @vite(['resources/css/consultar-agenda/app.css'])
@endsection

@section('content')

    @if($errors->any())
        <div class="alert alert-danger shadow text-center position-fixed top-0 start-50 translate-middle-x mt-3 animate-alert" style="max-width: 90%;">
            <strong>Ops!</strong> Corrija os itens abaixo:
            <ul class="mb-0 mt-1 list-unstyled">
                @foreach($errors->all() as $error)
                    <li><i class="bi bi-exclamation-circle-fill me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success text-center shadow position-fixed top-0 start-50 translate-middle-x mt-3 animate-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="mx-3 mb-2 mw-100">

        <div class="row">

            <!-- HEADER -->
            <x-page-title>
                <span id="button-novo-agendamento-header" onclick="window.location.href = '/psicologia/criar-agendamento'" class="btn btn-success">
                    <span>Novo Agendamento</span>
                </span>
            </x-page-title>

            <div class="col-12 shadow-lg shadow-dark px-4 pt-2 bg-body-tertiary rounded">
            
                <!-- FORM DE FILTRO -->
                <form id="search-form" class="w-100">
                    
                    <div class="row g-2">
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input id="search-input" name="search" type="search" class="form-control" placeholder="Nome ou CPF do paciente" />
                                <button type="button" class="btn btn-outline-secondary clear-input" data-target="search-input"><i class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-workspace"></i></span>
                                <input id="aluno-input" name="aluno" type="search" class="form-control" placeholder="Nome/Matrícula do aluno" />
                                <button type="button" class="btn btn-outline-secondary clear-input" data-target="aluno-input"><i class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                <input id="date-input" name="date" type="text" class="form-control" placeholder="Data" />
                                <button type="button" class="btn btn-outline-secondary clear-input" data-target="date-input"><i class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock-history"></i></span>
                                <input id="start-time-input" name="start_time" type="text" class="form-control" placeholder="Hora Início" />
                                <button type="button" class="btn btn-outline-secondary clear-input" data-target="start-time-input"><i class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                <input id="end-time-input" name="end_time" type="text" class="form-control" placeholder="Hora Fim" />
                                <button type="button" class="btn btn-outline-secondary clear-input" data-target="end-time-input"><i class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-list-check"></i></span>
                                <select id="status-input" name="status" class="form-select">
                                    <option value="" selected>Todos os Status</option>
                                    <option value="Agendado">Agendado</option>
                                    <option value="Presente">Presente</option>
                                    <option value="Remarcado">Remarcado</option>
                                    <option value="Cancelado">Cancelado</option>
                                    <option value="Finalizado">Finalizado</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary clear-input" data-target="status-input"><i class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-briefcase"></i></span>
                                <input id="service-input" name="service" type="text" class="form-control" placeholder="Serviço" />
                                <button type="button" class="btn btn-outline-secondary clear-input" data-target="service-input"><i class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input id="local-input" name="local" type="text" class="form-control" placeholder="Local" />
                                <button type="button" class="btn btn-outline-secondary clear-input" data-target="local-input"><i class="bi bi-x"></i></button>
                            </div>
                        </div>
                        <div class="col-12 col-lg-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Pesquisar
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btnClearFilters">
                                Limpar
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                <!-- CONTAINER DA TABELA -->
                <!-- Tabela para telas grandes -->
                <div id="tabela" class="border rounded d-none d-lg-block">
                    <table class="table table-hover table-bordered align-middle table-cards" id="tabela-container">
                        <thead>
                            <tr>
                                <th data-sort="paciente">Paciente</th>
                                <th data-sort="aluno">Aluno</th>
                                <th data-sort="servico">Serviço</th>
                                <th data-sort="data">Data</th>
                                <th data-sort="horaIni">Início</th>
                                <th data-sort="horaFim">Fim</th>
                                <th data-sort="local">Local</th>
                                <th data-sort="status">Status</th>
                                <th data-sort="reagendamento">Reagendamento?</th>
                                <th data-sort="valor">Valor</th>
                                <th data-sort="pago">Pago?</th>
                                <th data-sort="valorPago">Valor Pago</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="agendamentos-tbody">
                            <tr>
                                <td colspan="13" class="text-center">Nenhuma pesquisa realizada ainda.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="limit-container" class="d-flex flex-row justify-content-between align-items-center mt-2">
                    <div id="contador-registros">
                        <span class="text-muted">Total de registros: 0</span>
                    </div>
                    <div id="limitador-registros" class="mb-2 d-flex flex-row align-items-center justify-content-center gap-2">
                        <label for="limit-select" class="form-label mb-0">Mostrar</label>
                        <select id="limit-select" class="form-select form-select-sm" style="width: auto;">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <!-- Accordion para mobile -->
                <div id="accordion-container" class="d-lg-none">
                    <div class="accordion" id="agendamentosAccordion"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @vite(['resources/js/consultar-agenda/app.js'])
@endsection