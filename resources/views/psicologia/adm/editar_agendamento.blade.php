<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="/favicon_faesa.png">
    <title>Detalhes do Agendamento #{{ $agendamento->ID_AGENDAMENTO }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <style>
        .card-header h5 { margin-bottom: 0; font-size: 1.1rem; font-weight: 600; }
        .details-grid { display: grid; grid-template-columns: 150px 1fr; gap: 1rem; align-items: center;}
        .details-grid dt { font-weight: 700; color: #555; }
        .details-grid dd { margin-bottom: 0; }
        .card-footer { font-size: 0.8rem; color: #6c757d; }
        /* Esconde o select original do TomSelect até ser inicializado */
        .ts-hidden-accessible { display: none; }
    </style>

</head>

<body class="bg-body-secondary">

    <!-- COMPONENT NAVBAR -->
    @include('components.navbar')

    <!-- EM CASO DE ERROS -->
    @if ($errors->any())
        <div id="alert-error" class="alert alert-danger shadow text-center position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050; max-width: 90%;">
            <strong>Ops!</strong> Corrija os itens abaixo:
            <ul class="mb-0 mt-1 list-unstyled">
                @foreach ($errors->all() as $error)
                    <li><i class="bi bi-exclamation-circle-fill me-1"></i> {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div id="alert-session-error" class="alert alert-danger shadow text-center position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050; max-width: 90%;">
            <strong>Atenção:</strong> {{ session('error') }}
        </div>
    @endif

    <div class="container ms-3 me-3 mw-100">
        <div class="row">

            <x-page-title>
            </x-page-title>

            <form method="POST" action="{{ route('agendamento.update') }}">
                @csrf
                @method('PUT')

                <input type="hidden" id="id_agendamento" name="ID_AGENDAMENTO" value="{{ $agendamento->ID_AGENDAMENTO }}">
                <input type="hidden" id="id_clinica" name="ID_CLINICA" value="{{ $agendamento->ID_CLINICA }}">

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><i class="bi bi-calendar-check me-2"></i>Detalhes do Atendimento</h5>
                            </div>
                            <div class="card-body">
                                <dl class="details-grid">
                                    <dt>Serviço</dt>

                                    <!-- SERVICO DA CLINICA - DISCIPLINA -->
                                    <dd>
                                        <input type="text" class="form-control view-mode" 
                                            value="{{ $agendamento->servico->SERVICO_CLINICA_DESC ?? '' }}" disabled>
                                        <div class="edit-mode d-none">
                                            <select id="select-servico" name="ID_SERVICO" placeholder="Selecione ou busque um serviço...">
                                                @if($agendamento->servico)
                                                    <option value="{{ $agendamento->servico->ID_SERVICO_CLINICA }}" selected>
                                                        {{ $agendamento->servico->SERVICO_CLINICA_DESC }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                    </dd>
                                    
                                    <dt>Data</dt>

                                        <!-- DATA DO AGENDAMENTO -->
                                        <dd>
                                            <input type="date" id="DT_AGEND" class="form-control editable-field" name="DT_AGEND" 
                                                value="{{ $agendamento->DT_AGEND->format('Y-m-d') }}" disabled>
                                        </dd>

                                        <!-- HORÁRIO INICIAL -->
                                        <dt>Horário Início</dt>
                                        <dd>
                                            <input type="time" id="HR_AGEND_INI" class="form-control editable-field" name="HR_AGEND_INI" 
                                                value="{{ \Carbon\Carbon::parse($agendamento->HR_AGEND_INI)->format('H:i') }}" disabled>
                                        </dd>

                                        <!-- HORÁRIO FINAL -->
                                        <dt>Horário Fim</dt>
                                        <dd>
                                            <input type="time" id="HR_AGEND_FIN" class="form-control editable-field" name="HR_AGEND_FIN" 
                                                value="{{ \Carbon\Carbon::parse($agendamento->HR_AGEND_FIN)->format('H:i') }}" disabled>
                                        </dd>
                                    
                                    <dt>Status</dt>

                                    <!-- STATUS DA AGENDA -->
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
                                            class="btn btn-secondary btn-sm mt-2 w-100" 
                                            target="_blank" 
                                            title="Ir para agendamento anterior">
                                                <i class="bi bi-box-arrow-up-right me-2"></i>
                                                Ver Agendamento da Remarcação
                                            </a>
                                        @endif
                                        
                                    </dd>

                                </dl>
                            </div>
                        </div>

                        <!-- OBSERVAÇÕES -->
                        <div class="card mb-4">
                            <div class="card-header"><h5><i class="bi bi-card-text me-2"></i>Observações</h5></div>
                            <div class="card-body">
                                <textarea class="form-control editable-field" name="OBSERVACOES" rows="4" disabled>{{ $agendamento->OBSERVACOES }}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header"><h5><i class="bi bi-people me-2"></i>Envolvidos e Localização</h5></div>
                            <div class="card-body">
                                <dl class="details-grid">

                                    <!-- PACIENTE -->
                                    <dt>Paciente</dt>
                                    <dd>
                                        <input type="text" class="form-control view-mode" 
                                            value="{{ $agendamento->paciente->NOME_COMPL_PACIENTE ?? '' }}" readonly>
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
                                    
                                    <!-- ALUNO -->
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

                                    <!-- CLÍNICA -->
                                    <dt>Clínica</dt>
                                    <dd>
                                        <input type="text" class="form-control" value="Psicologia" disabled>
                                    </dd>
                                    
                                    <!-- SALA - LOCAL DA AGENDA -->
                                    <dt>Sala</dt>
                                    <dd>
                                        <input type="text" class="form-control view-mode" 
                                            value="{{ $agendamento->LOCAL ?? '' }}" disabled>
                                        <div class="edit-mode d-none">
                                            <select id="select-local" name="ID_SALA" placeholder="Selecione ou busque uma sala...">
                                            @if($agendamento->sala)
                                                    <option value="{{ $agendamento->ID_SALA }}" selected>
                                                        {{ $agendamento->LOCAL }}
                                                    </option>
                                                @else
                                                    <option value="{{$agendamento->ID_SALA}}" selected>{{$agendamento->ID_SALA}}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </dd>

                                </dl>
                            </div>
                        </div>

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

    <!-- FLATPICKR -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/pt.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnEditar = document.getElementById('btn-editar');
        const btnSalvar = document.getElementById('btn-salvar');

        // Flag para garantir que a inicialização ocorra apenas uma vez
        let editModeInicializado = false;

        // Traduz o Flatpickr para português assim que a página carrega
        flatpickr.localize(flatpickr.l10ns.pt);

        btnEditar.addEventListener('click', function () {
            // Habilita campos simples
            document.querySelectorAll('.editable-field').forEach(el => el.removeAttribute('disabled'));

            // Alterna a visibilidade dos campos
            document.querySelectorAll('.view-mode').forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('d-none'));
            
            // Troca os botões
            btnSalvar.classList.remove('d-none');
            this.classList.add('d-none');

            // Inicializa os plugins (apenas na primeira vez)
            if (!editModeInicializado) {
                inicializarTomSelects();
                inicializarFlatpickr(); // <--- CHAMADA DA NOVA FUNÇÃO
                editModeInicializado = true;
            }
        });

        /**
         * FUNÇÃO PARA INICIALIZAR O FLATPICKR
         */
        function inicializarFlatpickr() {
            const commonDateConfig = {
                altInput: true,       
                altFormat: "d/m/Y",  
                dateFormat: "Y-m-d", 
                allowInput: true,
                minDate: "today"
            };

            const commonTimeConfig = {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 15,
                allowInput: true
            };

            // Aplica a configuração aos campos pelos seus IDs
            flatpickr("#DT_AGEND", commonDateConfig);
            flatpickr("#HR_AGEND_INI", commonTimeConfig);
            flatpickr("#HR_AGEND_FIN", commonTimeConfig);
        }


        /**
         * FUNÇÃO PARA INICIALIZAR OS TOMSELECTS (já existente)
         */
        function inicializarTomSelects() {
            // --- SELECT PACIENTE ---
            new TomSelect('#select-paciente', {
                valueField: 'ID_PACIENTE',
                labelField: 'NOME_COMPL_PACIENTE',
                searchField: ['NOME_COMPL_PACIENTE', 'CPF_PACIENTE'],
                create: false,
                load: (query, callback) => {
                    if (query.length < 2) return callback();
                    const url = `/psicologia/consultar-paciente/buscar-nome-cpf?search=${encodeURIComponent(query)}`;
                    fetch(url).then(response => response.json()).then(json => callback(json)).catch(() => callback());
                },
                render: {
                    option: (data, escape) => `<div><strong>${escape(data.NOME_COMPL_PACIENTE)}</strong><small class="d-block text-muted">${escape(data.CPF_PACIENTE || '')}</small></div>`,
                    item: (data, escape) => `<div>${escape(data.NOME_COMPL_PACIENTE)}</div>`,
                    no_results: function(data, escape) {
                        const query = encodeURIComponent(data.input || '');
                        return `
                            <div class="no-results">
                                Nenhum paciente encontrado. 
                                <a href="/psicologia/criar-paciente?nome_compl_paciente=${query}" 
                                target="_blank" class="text-primary fw-bold">
                                Criar novo paciente
                                </a>
                            </div>
                        `
                    }
                }
            });

            // --- SELECT SERVIÇO ---
            new TomSelect('#select-servico', {
                valueField: 'ID_SERVICO_CLINICA',
                labelField: 'SERVICO_CLINICA_DESC',
                searchField: ['SERVICO_CLINICA_DESC'],
                create: false,
                load: (query, callback) => {
                    const url = `/psicologia/pesquisar-servico?search=${encodeURIComponent(query)}`;
                    fetch(url).then(r => r.json()).then(j => callback(j)).catch(() => callback());
                },

                render: {
                    no_results: function(data, escape) {
                        const query = encodeURIComponent(data.input || '');
                        return `
                        <div class="no-results">
                                    Nenhum serviço encontrado. 
                                    <a href="/psicologia/criar-servico?SERVICO_CLINICA_DESC=${query}" 
                                    target="_blank" class="text-primary fw-bold">
                                    Criar novo serviço
                                    </a>
                                </div>
                        `
                    }
                }
            });

            // --- SELECT ALUNO ---
            new TomSelect('#select-aluno', {
                valueField: 'ID_ALUNO',
                labelField: 'NOME_COMPL',
                searchField: ['ID_ALUNO', 'NOME_COMPL'],
                create: false,
                load: (query, callback) => {
                    const url = `/psicologia/listar-alunos?search=${encodeURIComponent(query)}`;
                    fetch(url).then(r => r.json()).then(j => callback(j)).catch(() => callback());
                },
                render: {
                    option: (data, escape) => `<div>${escape(data.NOME_COMPL)} - ${escape(data.ID_ALUNO)}</div>`,
                    item: (data, escape) => `<div>${escape(data.NOME_COMPL)}</div>`
                },
                onChange: (value) => {
                    const hiddenInput = document.getElementById('id_aluno_input');
                    hiddenInput.value = value || '';
                }
            });

            // --- SELECT LOCAL ---
            new TomSelect('#select-local', {
                valueField: 'ID_SALA_CLINICA',
                labelField: 'DESCRICAO',
                searchField: ['DESCRICAO'],
                create: false,
                load: (query, callback) => {
                    const url = `/psicologia/pesquisar-local?search=${encodeURIComponent(query)}`;
                    fetch(url).then(r => r.json()).then(j => callback(j)).catch(() => callback());
                },
                render: {
                    no_results: function(data, escape) {
                        const query = encodeURIComponent(data.input || '');
                        return `<div class="no-results">
                                Nenhum local encontrado. 
                                <a href="/psicologia/criar-sala?DESCRICAO=${query}" 
                                target="_blank" class="text-primary fw-bold">
                                Criar nova sala
                                </a>
                            </div>`;
                    }
                }
            });
        }
    });
</script>

<!-- FORMATA VALOR DA AGENDA EDITADA -->
<script>
    document.getElementById('valor_edit_agenda').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        value = (value / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
        e.target.value = value; 
    });
</script>

</body>
</html>