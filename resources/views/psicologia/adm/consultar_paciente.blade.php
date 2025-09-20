@extends('layouts.app_adm')

@section('title', 'Consultar Paciente')

@section('styles')
    <!-- FULL CALENDAR  -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />

    <!-- FONTES -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />

    @vite(['resources/css/page-title-header/app.css'])
    @vite(['resources/css/consultar-paciente/app.css'])
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
                    <span onclick="window.location.href = '/psicologia/criar-paciente'" class="btn btn-success p-2 me-3" style="font-size: 15px;" >
                        <span>Novo Paciente</span>
                    </span>
            </x-page-title>

            <div class="col-12 shadow-lg shadow-dark px-4 pt-4 bg-body-tertiary rounded">

                <form id="search-form" class="w-100">
                    <div class="row g-2">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input id="search-input" name="search" type="search" class="form-control" placeholder="Nome ou CPF" />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                <input id="DT_NASC_PACIENTE-input" name="DT_NASC_PACIENTE" type="text" class="form-control" placeholder="Data de Nascimento"/>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-2">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-gender-ambiguous"></i></span>
                                <select id="sexo" name="SEXO_PACIENTE" class="form-select">
                                    <option value="" selected>Sexo</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Feminino</option>
                                    <option value="O">Outro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-2">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input id="telefone-input" name="FONE_PACIENTE" type="text" class="form-control" placeholder="Telefone" />
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-2">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-check2-circle"></i></span>
                                <select id="status-input" name="STATUS" class="form-select">
                                    <option value="" selected>Status</option>
                                    <option value="Em espera">Em espera</option>
                                    <option value="Em atendimento">Em atendimento</option>
                                    <option value="Finalizado">Finalizado</option>
                                    <option value="Inativo">Inativo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-lg-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">Pesquisar</button>
                            <button type="button" class="btn btn-outline-secondary flex-grow-1" id="btnCleanFilters">Limpar</button>
                        </div>
                    </div>
                </form>

                <hr>

                    <div id="tabela" class="border rounded d-none d-lg-block">
                        <table class="table table-hover table-bordered align-middle table-cards" id="tabela-container">
                            <thead>
                                <tr>
                                    <th data-sort="NOME_COMPL_PACIENTE">Nome</th>
                                    <th data-sort="CPF_PACIENTE">CPF</th>
                                    <th data-sort="DT_NASC_PACIENTE">Nascimento</th>
                                    <th data-sort="SEXO_PACIENTE">Sexo</th>
                                    <th data-sort="FONE_PACIENTE">Telefone</th>
                                    <th data-sort="E_MAIL_PACIENTE">Email</th>
                                    <th data-sort="STATUS">Status</th>
                                    <th id="acoes-column">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="pacientes-tbody">
                                <tr>
                                    <td colspan="8" class="text-center">Nenhuma pesquisa realizada ainda.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="limit-container">
                        <div id="contador-registros">
                            <span class="text-muted">Total de registros: 0</span>
                        </div>
                        <div id="limitador-registros">
                            <label for="limite-visualizacao" class="form-label mb-0">Mostrar</label>
                            <select id="limite-visualizacao" class="form-select form-select-sm" style="width: auto;">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>

                    <!-- Accordion (somente mobile) -->
                    <div id="pacientesAccordion" class="accordion d-lg-none mt-3"></div>
                    
            </div>
            
        </div>
    </div>

    @include('psicologia.adm.partials.modals_consulta_paciente')

@endsection

@section('scripts')
    @vite(['resources/js/consultar-paciente/app.js'])
@endsection