<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Consulta de Agendamento</title>

    <link rel="icon" type="image/png" href="/favicon_faesa.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* reset box sizing to avoid unexpected overflow calculations */
        *, *::before, *::after { box-sizing: border-box; }

        /* container that holds the table: enable independent horizontal scrolling */
        #tabela {
            overflow-x: auto;              /* habilita scroll horizontal sempre que necessário */
            overflow-y: auto;              /* mantém scroll vertical */
            -webkit-overflow-scrolling: touch;
            max-height: 65vh;
            border-radius: 0.375rem;
            box-sizing: border-box;
        }

        /* make table use a fixed layout so cells can shrink and wrap instead of forcing page overflow */
        .table-cards {
            width: 100%;
            table-layout: fixed; /* força colunas a dividir o espaço disponível */
            border-collapse: collapse;
            min-width: 0; /* importante para permitir encolhimento */
        }

        /* allow breaking long words / values inside cells */
        .table-cards th,
        .table-cards td {
            white-space: normal !important;   /* permite quebra de linha */
            word-break: break-word;           /* quebra palavras longas */
            overflow-wrap: anywhere;          /* ajuda em strings sem espaços */
            vertical-align: middle;
            min-width: 0;                     /* evita que células imponham largura mínima */
        }

        /* evitar que os grupos de input do formulário forcem largura mínima */
        #search-form .row > [class*="col-"] {
            min-width: 0; /* permite que colunas encolham */
        }
        #search-form .input-group,
        #search-form .form-control,
        #search-form .form-select,
        #search-form .btn {
            min-width: 0;
        }

        /* reduzir impacto das ações (botões) na largura da tabela */
        .agendamento-actions {
            min-width: 1px;
            white-space: nowrap; /* manter botões lado a lado */
        }

        .agendamento-actions .btn:hover {
            filter: brightness(85%);
            transition: filter 0.2s ease-in-out;
        }

        .shadow-dark {
            box-shadow: 0 0.75rem 1.25rem rgba(0,0,0,0.4) !important;
        }

        /* pequenas melhorias no visual das alerts animadas */
        @keyframes slideDownFadeOut {
            0%   { transform: translate(-50%, -100%); opacity: 0; }
            10%  { transform: translate(-50%, 0); opacity: 1; }
            90%  { transform: translate(-50%, 0); opacity: 1; }
            100% { transform: translate(-50%, -100%); opacity: 0; }
        }
        .animate-alert {
            animation: slideDownFadeOut 5s ease forwards;
            z-index: 1050;
        }

        /* mobile: manter seu comportamento atual (cards) — sem mudanças */
        @media (max-width: 992px) {
            .table-cards thead {
                display: none;
            }

            .table-cards tbody, .table-cards tr, .table-cards td {
                display: block;
                width: 100%;
            }

            .table-cards tr {
                margin-bottom: 1rem;
                border: 3px solid #131313ff;
                border-radius: 0.5rem;
                margin-top: 20px;
                box-shadow: 0 10px 4px rgba(0, 0, 0, 0.08);
                background-color: #fff;
            }

            .table-cards td {
                text-align: right;
                padding-left: 50%;
                position: relative;
                border: none;
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
                white-space: normal;
            }

            .table-cards td:not(:last-child) {
                border-bottom: 1px solid #eee;
            }

            .table-cards td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 1rem;
                font-weight: bold;
                text-align: left;
            }
        }
    </style>
</head>

<body class="bg-body-secondary">
    @include('components.navbar')

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

    <div class="container-fluid ms-3 me-3 mt-3">
        <div class="row">
            <x-page-title>
                    <p onclick="window.location.href = '/psicologia/criar-agendamento'" class="btn btn-success p-2 me-3" style="font-size: 15px;" >
                        <span>Novo Agendamento</span>
                    </p>
            </x-page-title>

            <div class="col-12 shadow-lg shadow-dark p-3 p-md-4 bg-body-tertiary rounded">
                
                <form id="search-form" class="mb-4">
                    <div class="row g-3">
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
                                <i class="bi bi-search"></i> Pesquisar
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="btnClearFilters">
                                Limpar
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="w-100">
                    <h5 class="mb-3 text-center">Resultados</h5>
                    <div id="tabela" class="border rounded">
                        <table class="table table-hover table-bordered align-middle mb-0 table-cards">
                            <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th>Paciente</th>
                                    <th>Aluno</th>
                                    <th>Serviço</th>
                                    <th>Data</th>
                                    <th>Início</th>
                                    <th>Fim</th>
                                    <th>Local</th>
                                    <th>Status</th>
                                    <th>Reagendamento?</th>
                                    <th>Valor</th>
                                    <th>Pago?</th>
                                    <th>Valor Pago</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="agendamentos-tbody">
                                <tr>
                                    <td colspan="13" class="text-center">Nenhuma pesquisa realizada ainda.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="limit-container">
                        <div id="contador-registros">
                            <span class="text-muted">Total de registros: 0</span>
                        </div>
                        <div id="limitador-registros">
                            <label for="limit-select" class="form-label mb-0 text-muted">Exibir:</label>
                            <select id="limit-select" class="form-select form-select-sm" style="width: auto;">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const agendamentosTbody = document.getElementById('agendamentos-tbody');
            const searchForm = document.getElementById('search-form');
            const limitSelect = document.getElementById('limit-select');
            const contadorRegistros = document.getElementById('contador-registros');

            function getFilters() {
                const formData = new FormData(searchForm);
                const params = new URLSearchParams(formData);
                params.append('limit', limitSelect.value);
                return params;
            }

            function carregarAgendamentos() {
                const params = getFilters();
                const url = `/psicologia/get-agendamento?${params.toString()}`;

                agendamentosTbody.innerHTML = `<tr><td colspan="13" class="text-center"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div> Carregando...</td></tr>`;

                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Erro na resposta da rede.');
                        return response.json();
                    })
                    .then(agendamentos => {
                        agendamentosTbody.innerHTML = '';
                        contadorRegistros.innerHTML = `<span class="text-muted">Total de registros: ${agendamentos.length}</span>`;

                        if (agendamentos.length === 0) {
                            agendamentosTbody.innerHTML = `<tr><td colspan="13" class="text-center">Nenhum agendamento encontrado para os filtros aplicados.</td></tr>`;
                            return;
                        }

                        agendamentos.forEach(ag => {
                            const paciente = ag.paciente ? ag.paciente.NOME_COMPL_PACIENTE : '-';
                            const aluno = ag.aluno ? ag.aluno.NOME_COMPL : (ag.ID_ALUNO || '-');
                            const servico = ag.servico ? ag.servico.SERVICO_CLINICA_DESC : '-';
                            const data = ag.DT_AGEND ? new Date(ag.DT_AGEND).toLocaleDateString('pt-BR', {timeZone: 'UTC'}) : '-';
                            const horaIni = ag.HR_AGEND_INI ? ag.HR_AGEND_INI.substring(0, 5) : '-';
                            const horaFim = ag.HR_AGEND_FIN ? ag.HR_AGEND_FIN.substring(0, 5) : '-';
                            const local = ag.LOCAL ?? '-';
                            const status = ag.STATUS_AGEND || '-';
                            const valor = ag.VALOR_AGEND ? parseFloat(ag.VALOR_AGEND).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '-';
                            const checkPagamento = ag.STATUS_PAG === 'S' ? '<span class="badge bg-success">Sim</span>' : '<span class="badge bg-danger">Não</span>';
                            const valorPagamento = ag.VALOR_PAG ? parseFloat(ag.VALOR_PAG).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '-';
                            const reagendamento = ag.ID_AGEND_REMARCADO != null ? 'Sim' : 'Não';
                            
                            const statusMap = {
                                'Agendado': { color: 'text-success', icon: 'bi-calendar-check' },
                                'Cancelado': { color: 'text-danger', icon: 'bi-calendar-x' },
                                'Presente': { color: 'text-primary', icon: 'bi-check2-circle' },
                                'Finalizado': { color: 'text-secondary', icon: 'bi-calendar2-check-fill' },
                                'Remarcado': { color: 'text-warning', icon: 'bi-arrow-repeat' }
                            };
                            const statusInfo = statusMap[status] || { color: 'text-muted', icon: 'bi-question-circle' };

                            const row = document.createElement('tr');
                            
                            row.innerHTML = `
                                <td data-label="Paciente">${paciente}</td>
                                <td data-label="Aluno">${aluno}</td>
                                <td data-label="Serviço">${servico}</td>
                                <td data-label="Data">${data}</td>
                                <td data-label="Início">${horaIni}</td>
                                <td data-label="Fim">${horaFim}</td>
                                <td data-label="Local">${local}</td>
                                <td data-label="Status" class="fw-bold ${statusInfo.color}"><i class="bi ${statusInfo.icon} me-1"></i>${status}</td>
                                <td data-label="Reagendamento?">${reagendamento}</td>
                                <td data-label="Valor">${valor}</td>
                                <td data-label="Pago?" class="text-md-center">${checkPagamento}</td>
                                <td data-label="Valor Pago">${valorPagamento}</td>
                                <td data-label="Ações" class="agendamento-actions">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="/psicologia/agendamento/${ag.ID_AGENDAMENTO}/editar" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                                        <form action="/psicologia/agendamento/${ag.ID_AGENDAMENTO}" method="POST" onsubmit="return confirm('Confirma a exclusão deste agendamento?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            `;
                            agendamentosTbody.appendChild(row);
                        });
                    })
                    .catch(error => {
                        console.error('Erro ao buscar agendamentos:', error);
                        agendamentosTbody.innerHTML = `<tr><td colspan="13" class="text-center text-danger">Falha ao carregar os dados. Tente novamente mais tarde.</td></tr>`;
                    });
            }

            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                carregarAgendamentos();
            });

            limitSelect.addEventListener('change', carregarAgendamentos);

            document.getElementById('btnClearFilters').addEventListener('click', () => {
                searchForm.reset();
                flatpickrInstances.forEach(instance => instance.clear());
                carregarAgendamentos();
            });

            carregarAgendamentos();
        });
    </script>
    
    <script>
        flatpickr.localize(flatpickr.l10ns.pt);
        const datePicker = flatpickr("#date-input", { dateFormat: "Y-m-d", altInput: true, altFormat: "d/m/Y", allowInput: true });
        const startTimePicker = flatpickr("#start-time-input", { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true });
        const endTimePicker = flatpickr("#end-time-input", { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true });
        const flatpickrInstances = [datePicker, startTimePicker, endTimePicker];
    </script>

    <script>
        document.querySelectorAll('.clear-input').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (!input) return;
                if (input._flatpickr) {
                    input._flatpickr.clear();
                } else {
                    input.value = "";
                }
                input.dispatchEvent(new Event('input'));
                input.dispatchEvent(new Event('change'));
            });
        });
    </script>
</body>
</html>