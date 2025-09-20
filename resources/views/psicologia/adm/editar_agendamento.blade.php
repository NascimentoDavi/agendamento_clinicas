@extends('layouts.app_adm')

@section('title', 'Detalhes do Agendamento #'.$agendamento->ID_AGENDAMENTO)

@section('styles')
    <!-- FONTES -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />

    <!-- MDB, Bootstrap Icons, TomSelect, Flatpickr -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    @vite(['resources/css/page-title-header/app.css'])
    @vite(['resources/css/detalhes-agenda   /app.css'])
@endsection

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger shadow text-center position-fixed top-0 start-50 translate-middle-x mt-3 animate-alert" style="max-width: 90%;">
            <strong>Ops!</strong> Corrija os itens abaixo:
            <ul class="mb-0 mt-1 list-unstyled">
                @foreach ($errors->all() as $error)
                    <li><i class="bi bi-exclamation-circle-fill me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger shadow text-center position-fixed top-0 start-50 translate-middle-x mt-3 animate-alert" style="max-width: 90%;">
            <strong>Atenção:</strong> {{ session('error') }}
        </div>
    @endif

    <div class="container mb-3 mw-100">
        <div class="row">
            <x-page-title></x-page-title>

            <form method="POST" action="{{ route('agendamento.update') }}">
                @csrf
                @method('PUT')

                <input type="hidden" id="id_agendamento" name="ID_AGENDAMENTO" value="{{ $agendamento->ID_AGENDAMENTO }}">
                <input type="hidden" id="id_clinica" name="ID_CLINICA" value="{{ $agendamento->ID_CLINICA }}">

                <div class="row">
                    <!-- COLUNA PRINCIPAL -->
                    <div class="col-lg-8">
                        <!-- CARD DETALHES DO ATENDIMENTO -->
                        <div class="card mb-2">
                            <div class="card-header">
                                <h5><i class="bi bi-calendar-check me-2"></i>Detalhes do Atendimento</h5>
                            </div>
                            <div class="card-body">
                                <dl class="details-grid">
                                    <dt>Serviço</dt>
                                    <dd>
                                        <input type="text" class="form-control view-mode" 
                                            value="{{ $agendamento->servico->SERVICO_CLINICA_DESC ?? '' }}" disabled>
                                        <div class="edit-mode d-none">
                                            <select id="select-servico" name="ID_SERVICO" placeholder="Selecione ou busque um serviço...">
                                                @if($agendamento->servico)
                                                    <option value="{{ $agendamento->servico->ID_SERVICO_CLINICA }}" selected>
                                                        {{ $agendamento->servico->SERVICO_CLINICA_DESC }}
                                                    </option>
                                                    <input type="hidden" name="ID_SERVICO" id="id_servico_input" value="{{ $agendamento->ID_SERVICO }}">
                                                @endif
                                            </select>
                                        </div>
                                    </dd>

                                    <dt>Data</dt>
                                    <dd>
                                        <input type="text" id="DT_AGEND" class="form-control editable-field" name="DT_AGEND" 
                                            value="{{ $agendamento->DT_AGEND->format('Y-m-d') }}" disabled>
                                    </dd>

                                    <dt>Horário Início</dt>
                                    <dd>
                                        <input type="time" id="HR_AGEND_INI" class="form-control editable-field" name="HR_AGEND_INI" 
                                            value="{{ \Carbon\Carbon::parse($agendamento->HR_AGEND_INI)->format('H:i') }}" disabled>
                                    </dd>

                                    <dt>Horário Fim</dt>
                                    <dd>
                                        <input type="time" id="HR_AGEND_FIN" class="form-control editable-field" name="HR_AGEND_FIN" 
                                            value="{{ \Carbon\Carbon::parse($agendamento->HR_AGEND_FIN)->format('H:i') }}" disabled>
                                    </dd>

                                    <dt>Status</dt>
                                    <dd>
                                        <select class="form-select editable-field" name="STATUS_AGEND" disabled>
                                            <option value="Agendado" {{ $agendamento->STATUS_AGEND == 'Agendado' ? 'selected' : '' }}>Agendado</option>
                                            <option value="Confirmado" {{ $agendamento->STATUS_AGEND == 'Confirmado' ? 'selected' : '' }}>Confirmado</option>
                                            <option value="Cancelado" {{ $agendamento->STATUS_AGEND == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                            <option value="Remarcado" {{ $agendamento->STATUS_AGEND == 'Remarcado' ? 'selected' : '' }}>Remarcado</option>
                                            <option value="Finalizado" {{ $agendamento->STATUS_AGEND == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                                        </select>

                                        @if ($agendamento->ID_AGEND_REMARCADO)
                                            <a href="{{ url('/psicologia/agendamento/' . $agendamento->ID_AGEND_REMARCADO . '/editar') }}" 
                                               class="btn btn-secondary btn-sm mt-2 w-100" target="_blank">
                                                <i class="bi bi-box-arrow-up-right me-2"></i>
                                                Ver Agendamento da Remarcação
                                            </a>
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>

                        <!-- CARD OBSERVAÇÕES -->
                        <div class="card mb-2">
                            <div class="card-header"><h5><i class="bi bi-card-text me-2"></i>Observações</h5></div>
                            <div class="card-body">
                                <textarea class="form-control editable-field" name="OBSERVACOES" rows="4" disabled>{{ $agendamento->OBSERVACOES }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- COLUNA LATERAL -->
                    <div class="col-lg-4">
                        <!-- CARD ENVOLVIDOS E LOCALIZAÇÃO -->
                        <div class="card mb-2">
                            <div class="card-header"><h5><i class="bi bi-people me-2"></i>Envolvidos e Localização</h5></div>
                            <div class="card-body">
                                <dl class="details-grid">
                                    <dt>Paciente</dt>
                                    <dd>
                                        <input type="text" class="form-control view-mode" value="{{ $agendamento->paciente->NOME_COMPL_PACIENTE ?? '' }}" readonly>
                                        <div class="edit-mode d-none">
                                            <select id="select-paciente" name="ID_PACIENTE" placeholder="Selecione ou busque um paciente...">
                                                @if($agendamento->paciente)
                                                    <option value="{{ $agendamento->paciente->ID_PACIENTE }}" selected>
                                                        {{ $agendamento->paciente->NOME_COMPL_PACIENTE }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                    </dd>

                                    <dt>Aluno(a)</dt>
                                    <dd>
                                        <input type="text" class="form-control view-mode" value="{{ $agendamento->aluno->NOME_COMPL ?? '-' }}" readonly>
                                        <div class="edit-mode d-none">
                                            <select id="select-aluno" name="ID_ALUNO" placeholder="Selecione ou busque um aluno...">
                                                @if($agendamento->ID_ALUNO)
                                                    <option value="{{ $agendamento->aluno->ALUNO }}" selected>
                                                        {{ $agendamento->aluno->NOME_COMPL }}
                                                    </option>
                                                    <input type="hidden" name="ID_ALUNO" id="id_aluno_input" value="{{ $agendamento->ID_ALUNO }}">
                                                @endif
                                            </select> 
                                        </div>
                                    </dd>

                                    <dt>Clínica</dt>
                                    <dd>
                                        <input type="text" class="form-control" value="Psicologia" disabled>
                                    </dd>

                                    <dt>Sala</dt>
                                    <dd>
                                        <input type="text" class="form-control view-mode" value="{{ $agendamento->LOCAL ?? '' }}" disabled>
                                        <div class="edit-mode d-none">
                                            <select id="select-local" name="ID_SALA" placeholder="Selecione ou busque uma sala...">
                                                @if($agendamento->sala)
                                                    <option value="{{ $agendamento->ID_SALA }}" selected>{{ $agendamento->LOCAL }}</option>
                                                @else
                                                    <option value="{{$agendamento->ID_SALA}}" selected>{{$agendamento->ID_SALA}}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>

                        <!-- CARD DETALHES FINANCEIROS -->
                        <div class="card mb-4">
                            <div class="card-header"><h5><i class="bi bi-currency-dollar me-2"></i>Detalhes Financeiros</h5></div>
                            <div class="card-body">
                                <dl class="details-grid">
                                    <dt>Valor</dt>
                                    <dd>
                                        <input type="text" class="form-control editable-field" name="VALOR_AGEND" id="valor_edit_agenda" 
                                            value="{{ $agendamento->VALOR_AGEND ? number_format($agendamento->VALOR_AGEND, 2, ',', '.') : '' }}" disabled>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTÕES -->
                <div class="mt-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="window.location='{{ route('listagem-agendamentos') }}'">
                        <i class="bi bi-arrow-left me-2"></i>Voltar
                    </button>
                    <div>
                        <button type="button" class="btn btn-primary" id="btn-editar">
                            <i class="bi bi-pencil-square me-2"></i>Editar
                        </button>
                        <button type="submit" class="btn btn-success d-none" id="btn-salvar">
                            <i class="bi bi-check2-square me-2"></i>Salvar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <!-- FLATPICKR -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/pt.js"></script>

    <!-- TOM SELECT -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    @vite(['resources/js/editar-agenda/app.js'])

    <script>
    document.getElementById('valor_edit_agenda').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        value = (value / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
        e.target.value = value; 
    });
    </script>
@endsection
