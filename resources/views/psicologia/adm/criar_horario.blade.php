@extends('layouts.app_adm')

@section('title', 'Criar Horário')

@section('styles')
    <!-- FULL CALENDAR  -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />

    <!-- FONTES -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />

    @vite(['resources/css/page-title-header/app.css'])
    @vite(['resources/css/criar-horario/app.css'])
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
    
    <div class="container mw-100">

        <div class="row">
            
            <x-page-title></x-page-title>

            <div class="col-12 shadow-lg shadow-dark p-4 bg-body-tertiary rounded">
                
                <!-- MENSAGEM DE AVISO -->
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        <strong>Atenção!</strong> Ao criar um bloqueio, agendamentos existentes no período não serão alterados. Lembre-se de remanejá-los manualmente.
                    </div>
                </div> 
                
                <form class="needs-validation" action="{{ route('criarHorario-Psicologia') }}" method="POST">

                    @csrf

                    <!-- ID CLINICA -->
                    <input type="hidden" name="ID_CLINICA" value="1">
                    
                    <div class="row g-3">

                        <div class="col-md-6 col-lg-4">

                            <!-- TIPO DE HORÁRIO -->
                            <label for="BLOQUEADO" class="form-label">Tipo de Horário</label>

                            <select id="BLOQUEADO" name="BLOQUEADO" class="form-select" required>
                                <option value="" disabled selected>Selecione...</option>
                                <option value="N">Horário de Atendimento</option>
                                <option value="S">Horário Bloqueado</option>
                            </select>

                        </div>

                        <!-- DESCRICAO   -->
                        <div class="col-md-6 col-lg-8">
                            <label for="DESCRICAO_HORARIO" class="form-label">Descrição</label>
                            <input type="text" id="DESCRICAO_HORARIO" name="DESCRICAO_HORARIO" class="form-control" value="{{ old('DESCRICAO_HORARIO') }}" required>
                        </div>

                        <!-- DATA INICIAL -->
                        <div class="col-md-6 col-lg-3">
                            <label for="DATA_HORARIO_INICIAL" class="form-label">Data Inicial</label>
                            <input type="text" id="DATA_HORARIO_INICIAL" name="DATA_HORARIO_INICIAL" class="form-control" value="{{ old('DATA_HORARIO_INICIAL') }}" placeholder="dd/mm/aaaa" required>
                        </div>
                        
                        <!-- DATA FINAL -->
                        <div class="col-md-6 col-lg-3">
                            <label for="DATA_HORARIO_FINAL" class="form-label">Data Final</label>
                            <input type="text" id="DATA_HORARIO_FINAL" name="DATA_HORARIO_FINAL" class="form-control" value="{{ old('DATA_HORARIO_FINAL') }}" placeholder="dd/mm/aaaa" required>
                        </div>
                        
                        <!-- HORÁRIO INICIAL -->
                        <div class="col-md-6 col-lg-3">
                            <label for="HR_HORARIO_INICIAL" class="form-label">Horário Inicial</label>
                            <input type="text" id="HR_HORARIO_INICIAL" name="HR_HORARIO_INICIAL" class="form-control" value="{{ old('HR_HORARIO_INICIAL') }}" placeholder="00:00" required>
                        </div>

                        <!-- HORÁRIO FINAL -->
                        <div class="col-md-6 col-lg-3">
                            <label for="HR_HORARIO_FINAL" class="form-label">Horário Final</label>
                            <input type="text" id="HR_HORARIO_FINAL" name="HR_HORARIO_FINAL" class="form-control" value="{{ old('HR_HORARIO_FINAL') }}" placeholder="00:00" required>
                        </div>
                        
                        <!-- OBSERVAÇÕES -->
                        <div class="col-12">
                            <label for="OBSERVACAO" class="form-label">Observações</label>
                            <textarea name="OBSERVACAO" id="OBSERVACAO" class="form-control" rows="2">{{ old('OBSERVACAO') }}</textarea>
                        </div>
                        
                        <!-- SUBMIT BUTTON -->
                        <div class="col-12 text-end">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-2"></i>Salvar Horário</button>
                        </div>

                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center mb-4">
                    <h2 class="fs-4 mb-0">Consulta e Edição de Horários</h2>
                </div>

                <!-- CAMPO DE BUSCA DE HORÁRIO -->
                <div class="mb-3">
                    <input type="search" id="search-horario" class="form-control" placeholder="Buscar horário por descrição..." />
                </div>

                <div class="table-responsive border rounded" style="max-height: 40vh; overflow-y: auto;">

                    <!-- TABELA COM HORÁRIOS CADASTRADOS -->
                    <table class="table table-hover table-bordered align-middle mb-0">

                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">

                            <tr>
                                <th>Descrição</th>
                                <th>Tipo</th>
                                <th>Ações</th>
                            </tr>

                        </thead>

                        <tbody id="horarios-tbody">
                            <tr><td colspan="3" class="text-center">Carregando...</td></tr>
                        </tbody>
                        
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('psicologia.adm.partials.modals_criar_horario')

@endsection

@section('scripts')
    @vite(['resources/js/criar-horario/app.js'])
@endsection