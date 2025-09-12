<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Agendamento</title>
    <link rel="icon" type="image/png" href="/favicon_faesa.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />

    <!-- TOM SELECT -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">

    <style>
        /* Estilos mantidos do arquivo original e adaptados */
        #servicos-list button {
            cursor: pointer;
        }

        #recorrenciaCampos.show {
            display: flex !important;
            animation: fadeInSlide 0.3s ease-in-out;
        }

        @keyframes fadeInSlide {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #alert-success, #alert-error {
            animation: slideDownFadeOut 5s ease forwards;
            max-width: 90%;
        }

        @keyframes slideDownFadeOut {
            0% { transform: translate(-50%, -100%); opacity: 0; }
            10% { transform: translate(-50%, 0); opacity: 1; }
            90% { transform: translate(-50%, 0); opacity: 1; }
            100% { transform: translate(-50%, -100%); opacity: 0; }
        }

        #info-observacao:hover {
            color: #0a58ca;
        }

        /* Estilos para limitar a altura das listas de busca */
        .list-local-option, .list-aluno-option, #pacientes-list .list-group {
            max-height: 200px;
            overflow-y: auto;
        }
        
        .shadow-dark {
            box-shadow: 0 0.75rem 1.25rem rgba(0,0,0,0.4) !important;
        }
    </style>
    
</head>

<body class="bg-body-secondary">

<!-- COMPONENT NAVBAR -->
@include('components.navbar')

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

@if(session('success'))
    <div id="alert-success" class="alert alert-success text-center shadow position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div id="alert-error" class="alert alert-danger text-center shadow position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1050; max-width: 90%;">
        {{ session('error') }}
    </div>
@endif

<div class="container ms-3 me-3 mw-100">

    <div class="row">
        
        <!-- COMPONENT DE HEADER DE CADA PÁGINA DAS VIEWS -->
        <x-page-title>
        </x-page-title>
        
        <div class="col-12 shadow-lg shadow-dark p-4 bg-body-tertiary rounded">

            <!-- FORM DE AGENDAMENTO -->
            <form action="{{ route('criarAgendamento-Psicologia') }}" method="POST" id="agendamento-form" class="w-100" validate>
                @csrf

                <input type="hidden" name="paciente_id" id="paciente_id" value="{{ old('paciente_id') }}"/>
                <input type="hidden" name="id_servico" id="id_servico" value="{{ old('id_servico') }}" />
                <input type="hidden" name="recorrencia" id="recorrencia"/>
                <input type="hidden" name="status_agend" value="Em aberto"/>
                <input type="hidden" name="usuario_id" value="{{ session('usuario')->USUARIO }}">

                <!-- PESQUISA POR PACIENTE -->
                <div class="mb-3 position-relative">
                    <label for="select-paciente" class="form-label">Paciente</label>
                    <select id="select-paciente" name="paciente_id" placeholder="Pesquisar paciente por nome ou CPF..." autocomplete="off" data-old-id="{{ old('paciente_id') }}"></select>
                </div>

                <div class="mb-2">
                    <h5 class="mb-0">Horário e Detalhes</h5>
                    <hr class="mt-1">
                </div>

                <div class="row g-3">

                    <!-- RECORRENCIA -->
                    <div class="col-12 mb-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" value="1" id="temRecorrencia" name="tem_recorrencia">
                            <label class="form-check-label fw-semibold" for="temRecorrencia">
                                <i class="bi bi-arrow-repeat me-1 text-primary"></i> Ativar recorrência
                            </label>
                        </div>
                    </div>

                    <!-- SELECAO DE SERVICO -->
                    <div class="col-sm-6 col-md-3 position-relative">
                        <label for="servico" class="form-label">
                            Serviço
                            <span id="info-observacao" style="display: none;">
                                <i class="bi bi-info-circle-fill"></i>
                            </span>
                        </label>
                        <select id="select-servico" name="id_servico" placeholder="Serviço do Atendimento..." autocomplete="off" data-old-id="{{ old('id_servico') }}" ></select>
                    </div>

                    <!-- DIA DO AGENDAMENTO -->
                    <div class="col-sm-6 col-md-3">
                        <label for="data" class="form-label">Dia</label>
                        <input type="text" id="data" name="dia_agend" class="form-control" value="{{ old('dia_agend') }}" placeholder="Selecione o dia">
                    </div>

                    <!-- HORÁRIO DE INÍCIO -->
                    <div class="col-sm-6 col-md-3">
                        <label for="hr_ini" class="form-label">Horário Início</label>
                        <input type="text" id="hr_ini" name="hr_ini" class="form-control" value="{{ old('hr_ini') }}">
                    </div>

                    <!-- HORÁRIO FINAL -->
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
                    
                    <div id="msg-recorrencia" class="alert alert-info mt-2 d-none">
                        Caso não selecione "dias da semana" e uma "duração" ou "data fim", serão gerados agendamentos semanais por 1 mês, no mesmo dia da semana do campo "Dia".
                    </div>

                    <!-- VALOR -->
                    <div class="col-sm-6 col-md-3">
                        <label for="valor_agend" class="form-label">Valor</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="text" name="valor_agend" id="valor_agend" class="form-control" placeholder="0,00" value="{{ old('valor_agend') }}">
                        </div>
                    </div>

                    <!-- SELEÇÃO DE LOCAL -->
                    <div class="col-sm-6 col-md-3 position-relative">
                        <input type="hidden" name="id_sala_clinica" id="id_sala_clinica">
                        <label for="select-local" class="form-label">Local</label>
                        <select id="select-local" name="id_sala_clinica" placeholder="Local do atendimento..." autocomplete="off" data-old-id="{{ old('id_sala_clinica') }}"></select>
                    </div>

                    <!-- SELEÇAO DE ALUNO -->
                    <div class="col-md-6 position-relative">
                         <input type="hidden" name="ID_ALUNO" id="ID_ALUNO">
                        <label for="select-aluno" class="form-label">Aluno</label>
                        <select id="select-aluno" name="ID_ALUNO" placeholder="Aluno do Atendimento..." autocomplete="off" data-old-id="{{ old('ID_ALUNO') }}"></select>
                        <small id="aluno-count" class="text-muted mt-1 d-block"></small>
                    </div>
                              
                    <!-- OBSERVAÇÕES -->
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>

<!-- TOM SELECT -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<!-- IMPEDE EMVIO CASO USUÁRIO TECLE ENTER SEM QUERER -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('agendamento-form');
        form.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && event.target.tagName.toLowerCase() !== 'textarea') {
                event.preventDefault();
            }
        });
    });
</script>

<script>
    const searchInput = document.getElementById('search-input');
    const pacientesList = document.getElementById('pacientes-list');
    const pacienteIdInput = document.getElementById('paciente_id');
</script>

<!-- BUSCA DE PACIENTES -->
<script>

    // BUSCA DE VALOR ANTIGO, CASO EXISTA
    function initializeTomSelectWithOldValue(selector, config) {
        
        const element = document.querySelector(selector);

        if (!element) return;

        const oldId = element.dataset.oldId;
        
        const ts = new TomSelect(element, config);

        if (oldId) ts.load(oldId);
        
        return ts;
    }

    // --- SELECT DE PACIENTE ---
    const pacienteSelect = initializeTomSelectWithOldValue('#select-paciente', {

        valueField: 'ID_PACIENTE',
        labelField: 'NOME_COMPL_PACIENTE',
        searchField: ['NOME_COMPL_PACIENTE', 'CPF_PACIENTE'],

        load: (query, callback) => {
            if (!query.length) return callback();
            const url = `/psicologia/consultar-paciente/buscar-nome-cpf?search=${encodeURIComponent(query)}`;
            fetch(url)
                .then(r => r.json())
                .then(json => callback(json))
                .catch(() => callback());
        },

        render: {
            no_results: function(data, escape) {
                const query = encodeURIComponent(data.input || ''); // pega o que o usuário digitou
                return `<div class="no-results">
                            Nenhum paciente encontrado. 
                            <a href="/psicologia/criar-paciente?nome_compl_paciente=${query}" 
                            target="_blank" class="text-primary fw-bold">
                            Criar novo paciente
                            </a>
                        </div>`;
            }
        }
    });

    // SELECT DE SERVICO
    const servicoSelect = initializeTomSelectWithOldValue('#select-servico', {
        valueField: 'ID_SERVICO_CLINICA',
        labelField: 'SERVICO_CLINICA_DESC',
        searchField: ['SERVICO_CLINICA_DESC'],

        load: (query, callback) => {
            if (!query.length) return callback();
            const url = `/psicologia/pesquisar-servico?search=${encodeURIComponent(query)}`;
            fetch(url)
                .then(r => r.json())
                .then(json => callback(json))
                .catch(() => callback());
        },

        render: {
            no_results: function(data, escape) {
                const query = encodeURIComponent(data.input || '');
                return `<div class="no-results">
                            Nenhum serviço encontrado. 
                            <a href="/psicologia/criar-servico?SERVICO_CLINICA_DESC=${query}" 
                            target="_blank" class="text-primary fw-bold">
                            Criar novo serviço
                            </a>
                        </div>`;
            }
        }
    });

    // SELECT DE SALA (LOCAL)
    const localSelect = initializeTomSelectWithOldValue('#select-local', {
        valueField: 'ID_SALA_CLINICA',
        labelField: 'DESCRICAO',
        searchField: ['DESCRICAO'],

        load: (query, callback) => {
            const servicoId = document.querySelector('#select-servico').value;
            if (!query.length && !servicoId) return callback();
            
            const url = `/psicologia/pesquisar-local?search=${encodeURIComponent(query)}&servico=${encodeURIComponent(servicoId)}`;
            fetch(url)
                .then(r => r.json())
                .then(json => callback(json))
                .catch(() => callback());
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

    // --- SELECT DE ALUNO ---
    const alunoSelect = initializeTomSelectWithOldValue('#select-aluno', {
        valueField: 'ID_ALUNO',
        labelField: 'NOME_COMPL',
        searchField: ['NOME_COMPL', 'ID_ALUNO'],
        load: (query, callback) => {

            // Pega o serviço selecionado
            const servicoValue = servicoSelect.getValue();
            let disciplina = '';
            if (servicoValue) {
                const selectedServico = servicoSelect.options[servicoValue];
                if (selectedServico && selectedServico.DISCIPLINA) {
                    disciplina = selectedServico.DISCIPLINA;
                }
            }

            // Monta a URL com o parâmetro disciplina
            const url = `/psicologia/listar-alunos?search=${encodeURIComponent(query)}&disciplina=${encodeURIComponent(disciplina)}`;

            fetch(url)
                .then(r => r.json())
                .then(json => callback(json))
                .catch(() => callback());
        }
    });

    alunoSelect.disable();

    // ATUALIZA O SELECT DE ALUNO QUANDO O SERVICO MUDAR
    servicoSelect.on('change', (value) => {
        if (value) {
            // Preenche o valor do serviço no campo valor_agend
            const selectedItem = servicoSelect.options[value];
            if (selectedItem && selectedItem.VALOR_SERVICO) {
                const valor = parseFloat(selectedItem.VALOR_SERVICO).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                document.getElementById('valor_agend').value = valor;
            } else {
                document.getElementById('valor_agend').value = '';
            }

            alunoSelect.clear();
            alunoSelect.enable();
            alunoSelect.load('');
        } else {
            alunoSelect.clear();
            alunoSelect.disable();
            document.getElementById('valor_agend').value = '';
        }
    });

    // Inicializa select de duração da recorrência
    new TomSelect('#duracao_meses_recorrencia', {});
</script>

<!-- SCRIPT DE SELEÇÃO DE RECORRÊNCIA -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const temRecorrenciaCheckbox = document.getElementById('temRecorrencia');
        const recorrenciaCampos = document.getElementById('recorrenciaCampos');
        const msgRecorrencia = document.getElementById('msg-recorrencia');
        const recorrenciaInput = document.getElementById('recorrencia');
        const diasSemanaBtns = document.querySelectorAll('#diasSemanaBtns button');
        let container = document.getElementById('diasSemanaContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'diasSemanaContainer';
            container.style.display = 'none';
            document.getElementById('agendamento-form').appendChild(container);
        }
        
        function atualizarDiasSelecionados() {
            container.innerHTML = '';
            const diasSelecionados = Array.from(diasSemanaBtns)
                .filter(btn => btn.classList.contains('active'))
                .map(btn => btn.getAttribute('data-dia'));
            diasSelecionados.forEach(dia => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'dias_semana[]';
                input.value = dia;
                container.appendChild(input);
            });
        }

        diasSemanaBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                this.classList.toggle('active');
                this.classList.toggle('btn-primary');
                this.classList.toggle('btn-outline-primary');
                atualizarDiasSelecionados();
            });
        });

        temRecorrenciaCheckbox.addEventListener('change', function() {
            if (this.checked) {
                recorrenciaCampos.classList.remove('d-none');
                msgRecorrencia.classList.remove('d-none');
                recorrenciaInput.value = crypto.randomUUID ? crypto.randomUUID() : 'uuid-fallback-' + Date.now();
            } else {
                recorrenciaCampos.classList.add('d-none');
                msgRecorrencia.classList.add('d-none');
                recorrenciaInput.value = '';
                diasSemanaBtns.forEach(btn => {
                    btn.classList.remove('active', 'btn-primary');
                    btn.classList.add('btn-outline-primary');
                });
                container.innerHTML = '';
                document.getElementById('data_fim_recorrencia').value = '';
                document.getElementById('duracao_meses_recorrencia').value = '';
            }
        });

        const selectDuracao = document.getElementById('duracao_meses_recorrencia');
        const inputDataFim = document.getElementById('data_fim_recorrencia');

        function atualizarCamposRecorrencia() {
            inputDataFim.disabled = selectDuracao.value !== '';
            selectDuracao.disabled = inputDataFim.value !== '';
        }

        selectDuracao.addEventListener('change', atualizarCamposRecorrencia);
        inputDataFim.addEventListener('input', atualizarCamposRecorrencia); // use input for flatpickr
    });
</script>

<!-- SCRIPT DO FLATPICKR -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr.localize(flatpickr.l10ns.pt);
        const commonDateConfig = {
            dateFormat: "Y-m-d", 
            altInput: true,
            altFormat: "d/m/Y",
            locale: "pt",
            allowInput: true,
        };
        const commonTimeConfig = {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minuteIncrement: 15,
            allowInput: true,
        };
        flatpickr("#data", {...commonDateConfig, minDate: "today"});
        flatpickr("#hr_ini", commonTimeConfig);
        flatpickr("#hr_fim", commonTimeConfig);
        flatpickr("#data_fim_recorrencia", {...commonDateConfig, minDate: "today"});
    });
</script>

<!-- FORMATAÇÃO DOS CAMPOS DE VALOR DE AGENDAMENTO -->
<script>
    document.getElementById('valor_agend').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        value = (value / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
        e.target.value = value;
    });

    document.addEventListener('DOMContentLoaded', function() {
        const alertSuccess = document.getElementById('alert-success');
        if (alertSuccess) setTimeout(() => alertSuccess.remove(), 4000);
        
        const alertError = document.getElementById('alert-error');
        if (alertError) setTimeout(() => alertError.remove(), 6000);
    });
</script>


</body>
</html>