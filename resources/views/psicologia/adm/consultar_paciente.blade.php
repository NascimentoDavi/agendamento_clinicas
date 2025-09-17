<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Consulta de Paciente</title>

    <link rel="icon" type="image/png" href="/favicon_faesa.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />

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
        .shadow-dark {
            box-shadow: 0 0.75rem 1.25rem rgba(0,0,0,0.4) !important;
        }
        #limit-container {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        #limitador-registros {
            display: flex;
            align-items: center;
            gap: 8px;
        }
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
        .modal-xxl {
           max-width: 90%;
        }
        .modal-body {
           max-height: 75vh;
           overflow-y: auto;
        }
        
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
                border: 3px solid #131313ff; /* cor da borda do card */
                border-radius: 0.5rem;  /* cantos arredondados */
                margin-top: 20px;
                box-shadow: 0 10px 4px rgba(0, 0, 0, 0.08); /* sombra leve para efeito card */
                background-color: #fff; /* fundo branco */
            }

            .table-cards td {
                text-align: right; 
                padding-left: 50%; 
                position: relative;
                border: none;
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
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
                    <p onclick="window.location.href = '/psicologia/criar-paciente'" class="btn btn-success p-2 me-3" style="font-size: 15px;" >
                        <span>Novo Paciente</span>
                    </p>
            </x-page-title>

            <div class="col-12 shadow-lg shadow-dark p-3 p-md-4 bg-body-tertiary rounded">

                <form id="search-form" class="w-100 mb-4">
                    <div class="row g-3">
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
                            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search"></i> Pesquisar</button>
                            <button type="button" class="btn btn-outline-secondary flex-grow-1" id="btnCleanFilters">Limpar</button>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="w-100">
                    <h5 class="mb-3">Resultados</h5>
                    <div id="tabela" class="border rounded" style="max-height: 55vh; overflow-y: auto;">
                        <table class="table table-hover table-bordered align-middle mb-0 table-cards">
                            <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Nascimento</th>
                                    <th>Sexo</th>
                                    <th>Telefone</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Ações</th>
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
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmEditModal" tabindex="-1" aria-labelledby="confirmEditModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmEditModalLabel">Editar Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p><strong id="modal-paciente-nome"></strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirm-edit-btn">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Inativar Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <strong id="modal-delete-nome"></strong>
                    <p class="text-danger small mt-2"><i class="bi bi-exclamation-triangle-fill"></i> Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-btn">Inativar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmAtivarModal" tabindex="-1" aria-labelledby="confirmAtivarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmAtivarModalLabel">Reativar Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <strong id="modal-ativar-nome"></strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirm-ativar-btn">Reativar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPacienteModal" tabindex="-1" aria-labelledby="editPacienteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Editar Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <form id="editPacienteForm">

                        <!-- INFORMAÇÕES PESSOAIS PACIENTE -->
                        <h6>Informações Pessoais</h6>
                        <hr>

                        <div class="row g-3">

                            <div class="col-md-6 form-floating">
                                <input type="text" class="form-control" id="editPacienteNome" name="nome" placeholder="Nome"  value="{{ old('name') }}">
                                <label for="editPacienteNome">Nome</label>
                            </div>

                            <div class="col-md-6 form-floating">
                                <input type="text" class="form-control" id="editPacienteStatus" name="status" readonly>
                                <label for="editPacienteStatus">Status</label>
                            </div>

                            <div class="col-md-6 form-floating">
                                <input type="text" class="form-control" id="editPacienteCPF" name="cpf" placeholder="CPF">
                                <label for="editPacienteCPF">CPF</label>
                            </div>

                            <div class="col-md-6 form-floating">
                                <input type="text" class="form-control" id="editPacienteDTNASC" name="dt_nasc" placeholder="Data de Nascimento">
                                <label for="editPacienteDTNASC">Data de Nascimento</label>
                            </div>

                            <div class="col-md-6 form-floating">
                                <select name="sexo" id="editPacienteSEXO" class="form-select" aria-label="Sexo">
                                    <option value="" selected>Selecione</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Feminino</option>
                                    <option value="O">Outro</option>
                                </select>
                                <label for="editPacienteSEXO">Sexo</label>
                            </div>

                        </div>

                        <!-- INFORMAÇÕES DO RESPONSÁVEL -->
                        <h6 class="mt-3">Informações do Responsável</h6>
                        <hr>

                         <div class="row g-3">

                            <div class="col-md-6 form-floating">
                                <input type="text" class="form-control" id="editPacienteResponsavelNome" name="nome_responsavel" placeholder="Nome do Responsável" value="{{ old('nome_responsavel') }}">
                                <label for="editPacienteResponsavelNome">Nome do Responsável</label>
                            </div>

                            <div class="col-md-6 form-floating">
                                <input type="text" class="form-control" id="editPacienteResponsavelCPF" name="cpf_responsavel" placeholder="CPF do Responsável">
                                <label for="editPacienteResponsavelCPF">CPF do Responsável</label>
                            </div>

                        </div>

                        <!-- INFORMAÇÕES DE ENDEREÇO -->
                        <h6 class="mt-4">Endereço</h6>
                        <hr>

                        <div class="row g-3">

                            <div class="col-md-4 form-floating">
                                <input type="text" class="form-control" id="editPacienteCEP" name="cep" placeholder="CEP">
                                <label for="editPacienteCEP">CEP</label>
                            </div>

                            <div class="col-md-8 form-floating">
                                <input type="text" class="form-control" id="editPacienteENDERECO" name="endereco" placeholder="Rua">
                                <label for="editPacienteENDERECO">Rua</label>
                            </div>

                            <div class="col-md-4 form-floating">
                                <input type="text" class="form-control" id="editPacienteNUM" name="num" placeholder="Número">
                                <label for="editPacienteNUM">Número</label>
                            </div>

                            <div class="col-md-8 form-floating">
                                <input type="text" class="form-control" id="editPacienteCOMPLEMENTO" name="complemento" placeholder="Complemento">
                                <label for="editPacienteCOMPLEMENTO">Complemento</label>
                            </div>

                            <div class="col-md-6 form-floating">
                                <input type="text" class="form-control" id="editPacienteBAIRRO" name="bairro" placeholder="Bairro">
                                <label for="editPacienteBAIRRO">Bairro</label>
                            </div>

                            <div class="col-md-6 form-floating">
                                <input type="text" id="editPacienteMUNICIPIO" name="municipio" class="form-control" placeholder="Município">
                                <label for="editPacienteMUNICIPIO">Município</label>
                            </div>

                            <div class="col-md-2 form-floating">
                                <input type="text" class="form-control" id="editPacienteUF" name="uf" placeholder="UF">
                                <label for="editPacienteUF">UF</label>
                            </div>

                        </div>

                        <!-- INFORMAÇÕES DE CONTATO -->
                        <h6 class="mt-4">Contato</h6>
                        <hr>

                        <div class="row g-3">

                            <div class="col-md-6 form-floating">
                                <input type="text" id="editPacienteCELULAR" name="celular" class="form-control" placeholder="Celular" />
                                <label for="editPacienteCELULAR">Celular</label>
                            </div>

                            <div class="col-md-6 form-floating">
                                <input type="email" id="editPacienteEMAIL" name="email" class="form-control" placeholder="Email" />
                                <label for="editPacienteEMAIL">Email</label>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="historicoPacienteModal" tabindex="-1" aria-labelledby="historicoPacienteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xxl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historicoPacienteModalLabel">Histórico de Agendamentos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Paciente:</strong> <span id="nomePacienteHistorico"></span></p>
                    <div id="tabelaHistoricoPaciente">
                        <table class="table table-bordered table-cards">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Hora Início</th>
                                    <th>Hora Fim</th>
                                    <th>Serviço</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="historicoAgendamentosBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.js"></script>

    <script>
        function formatarDataBR(dateStr) {
            if (!dateStr) return '-';
            const cleanedDate = dateStr.split('T')[0];
            const [year, month, day] = cleanedDate.split('-');
            return `${day}/${month}/${year}`;
        }

        window.addEventListener('DOMContentLoaded', () => {
            const searchForm = document.getElementById('search-form');
            const searchInput = document.getElementById('search-input');
            const dt_nasc_paciente_input = document.getElementById('DT_NASC_PACIENTE-input');
            const statusInput = document.getElementById('status-input');
            const sexoInput = document.getElementById('sexo');
            const telefoneInput = document.getElementById('telefone-input');
            const pacientesTbody = document.getElementById('pacientes-tbody');
            const limiteSelect = document.getElementById('limite-visualizacao');
            let selectedPaciente = null;
            let limiteRegistros = limiteSelect ? (parseInt(limiteSelect.value) || 10) : 10; 

            function ativarEventosEditar() {

                document.querySelectorAll('.editar-btn').forEach(button => {
                    
                    button.addEventListener('click', () => {
                        
                        selectedPaciente = {
                            id: button.getAttribute('data-id'),
                            status: button.getAttribute('data-status') ?? 'nada',
                            nome: button.getAttribute('data-nome'),
                            cpf: button.getAttribute('data-cpf'),
                            dt_nasc: button.getAttribute('data-dt_nasc'),
                            sexo: button.getAttribute('data-sexo'),
                            endereco: button.getAttribute('data-endereco'),
                            num: button.getAttribute('data-num'),
                            complemento: button.getAttribute('data-complemento'),
                            bairro: button.getAttribute('data-bairro'),
                            uf: button.getAttribute('data-uf'),
                            cep: button.getAttribute('data-cep'),
                            celular: button.getAttribute('data-celular'),
                            email: button.getAttribute('data-email'),
                            municipio: button.getAttribute('data-municipio'),
                            nome_responsavel: button.getAttribute('data-nome-responsavel'),
                            cpf_responsavel: button.getAttribute('data-cpf-responsavel'),
                        };

                        document.getElementById('modal-paciente-nome').textContent = `Deseja editar o paciente: ${selectedPaciente.nome}?`;
                        new bootstrap.Modal(document.getElementById('confirmEditModal')).show();

                    });
                });
            }

            function ativarEventosDeletar() {
                document.querySelectorAll('.excluir-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        selectedPaciente = { id: button.getAttribute('data-id'), nome: button.getAttribute('data-nome') };
                        document.getElementById('modal-delete-nome').textContent = `Deseja inativar o paciente: ${selectedPaciente.nome}?`;
                        new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
                    });
                });
            }

            function ativarEventosAtivar() {
                document.querySelectorAll('.ativar-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        selectedPaciente = { id: button.getAttribute('data-id'), nome: button.getAttribute('data-nome') };
                        document.getElementById('modal-ativar-nome').textContent = `Deseja ativar o paciente: ${selectedPaciente.nome}?`;
                        new bootstrap.Modal(document.getElementById('confirmAtivarModal')).show();
                    });
                });
            }

            function montarQueryParams(params) {
                return Object.entries(params)
                    .filter(([_, v]) => v !== null && String(v).trim() !== '')
                    .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
                    .join('&');
            }

            function buscarPacientes() {
                if (!pacientesTbody) return;
                const filtros = {
                    search: searchInput ? searchInput.value.trim() : '',
                    DT_NASC_PACIENTE: dt_nasc_paciente_input ? dt_nasc_paciente_input.value : '',
                    STATUS: statusInput ? statusInput.value : '-',
                    SEXO_PACIENTE: sexoInput ? sexoInput.value : '',
                    FONE_PACIENTE: telefoneInput ? telefoneInput.value : '',
                };
                const queryString = montarQueryParams(filtros);
                fetch(`/psicologia/consultar-paciente/buscar?${queryString}`)
                    .then(res => { if (!res.ok) throw new Error(`HTTP ${res.status}`); return res.json(); })
                    .then(pacientes => {
                        pacientesTbody.innerHTML = '';
                        if (!pacientes || pacientes.length === 0) {
                            pacientesTbody.innerHTML = `<tr><td colspan="8" class="text-center">Nenhum paciente encontrado.</td></tr>`;
                            document.getElementById('contador-registros').innerHTML = `<span>Total: 0</span>`;
                            return;
                        }
                        const limite = parseInt(limiteSelect.value);

                        const pacientesVisiveis = pacientes.slice(0, limite);
                        
                        document.getElementById('contador-registros').innerHTML = `<span>Mostrando ${pacientesVisiveis.length} de ${pacientes.length}</span>`;
                        
                        pacientesVisiveis.forEach(paciente => {
                            
                            const row = document.createElement('tr');

                            const isInativo = paciente.STATUS === 'Inativo';

                            // DEPENDENDO DO STATUS DO PACIENTE, MOSTRA UM BOTÃO
                            const btnStatus = isInativo 
                            
                                ? `<button type="button" class="btn btn-sm btn-success ativar-btn" data-id="${paciente.ID_PACIENTE}" data-nome="${paciente.NOME_COMPL_PACIENTE ?? 'Paciente'}"><i class="bi bi-check2"></i></button>`

                                : `<button type="button" class="btn btn-sm btn-danger excluir-btn" data-id="${paciente.ID_PACIENTE}" data-nome="${paciente.NOME_COMPL_PACIENTE ?? 'Paciente'}"><i class="bi bi-trash"></i></button>`;
                                
                            // CONTEÚDO DAS LINHAS DA TABELA
                            row.innerHTML = `

                                <td data-label="Nome">${paciente.NOME_COMPL_PACIENTE}</td>

                                <td data-label="CPF">${paciente.CPF_PACIENTE}</td>
                                
                                <td data-label="Nascimento">${paciente.DT_NASC_PACIENTE ? formatarDataBR(paciente.DT_NASC_PACIENTE) : '-'}</td>
                                
                                <td data-label="Sexo">${paciente.SEXO_PACIENTE ?? '-'}</td>
                                
                                <td data-label="Telefone">${paciente.FONE_PACIENTE ?? '-'}</td>
                                
                                <td data-label="Email">${paciente.E_MAIL_PACIENTE ?? '-'}</td>
                                
                                <td data-label="Status">${paciente.STATUS ?? '-'}</td>
                                
                                <td data-label="Ações" class="actions-cell">

                                    <div class="d-flex flex-wrap justify-content-center gap-1">

                                        <button type="button" class="btn btn-sm btn-warning editar-btn"

                                            data-id="${paciente.ID_PACIENTE}" data-status="${paciente.STATUS ?? '-'}" data-nome="${paciente.NOME_COMPL_PACIENTE ?? 'Paciente'}"
                                            
                                            data-cpf="${paciente.CPF_PACIENTE ?? ''}" data-dt_nasc="${paciente.DT_NASC_PACIENTE ?? ''}" data-sexo="${paciente.SEXO_PACIENTE ?? ''}"

                                            data-endereco="${paciente.ENDERECO ?? ''}" data-num="${paciente.END_NUM ?? ''}" data-complemento="${paciente.COMPLEMENTO ?? ''}"

                                            data-bairro="${paciente.BAIRRO ?? ''}" data-uf="${paciente.UF ?? ''}" data-cep="${paciente.CEP ?? ''}"

                                            data-celular="${paciente.FONE_PACIENTE ?? ''}" data-email="${paciente.E_MAIL_PACIENTE ?? ''}" data-municipio="${paciente.MUNICIPIO ?? ''}"

                                            data-nome-responsavel="${paciente.NOME_RESPONSAVEL ?? ''}"

                                            data-cpf-responsavel="${paciente.CPF_RESPONSAVEL ?? ''}"
                                        >

                                            <i class="bi bi-pencil"></i>

                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary historico-btn" data-id="${paciente.ID_PACIENTE}" data-nome="${paciente.NOME_COMPL_PACIENTE}">
                                            <i class="bi bi-clock-history"></i>
                                        </button>
                                        ${btnStatus}
                                    </div>
                                </td>
                            `;
                            pacientesTbody.appendChild(row);
                        });
                        ativarEventosEditar();
                        ativarEventosDeletar();
                        ativarEventosAtivar();
                    })
                    .catch(err => {
                        console.error(err);
                        pacientesTbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Erro ao buscar pacientes.</td></tr>`;
                    });
            }

            function abrirModalEdicao() {

                if (!selectedPaciente) return;

                document.getElementById('editPacienteNome').value = selectedPaciente.nome || '';

                let status = document.getElementById('editPacienteStatus').value = selectedPaciente.status || '';

                const cpfInput = document.getElementById('editPacienteCPF');
                
                if (cpfInput) 

                    {
                        // CASO PACIENTE ESTEJA INATIVO, PERMITE EDITAR CPF
                        cpfInput.value = selectedPaciente.cpf || ''; cpfInput.readOnly = (status !== 'Inativo');
                    }
                    
                    document.getElementById('editPacienteDTNASC').value = selectedPaciente.dt_nasc ? selectedPaciente.dt_nasc.split('T')[0] : '';
                    document.getElementById('editPacienteSEXO').value = selectedPaciente.sexo || '';
                    document.getElementById('editPacienteENDERECO').value = selectedPaciente.endereco || '';
                    document.getElementById('editPacienteNUM').value = selectedPaciente.num || '';
                    document.getElementById('editPacienteCOMPLEMENTO').value = selectedPaciente.complemento || '';
                    document.getElementById('editPacienteBAIRRO').value = selectedPaciente.bairro || '';
                    document.getElementById('editPacienteUF').value = selectedPaciente.uf || '';
                    document.getElementById('editPacienteCEP').value = selectedPaciente.cep || '';
                    document.getElementById('editPacienteCELULAR').value = selectedPaciente.celular || '';
                    document.getElementById('editPacienteEMAIL').value = selectedPaciente.email || '';
                    document.getElementById('editPacienteMUNICIPIO').value = selectedPaciente.municipio || '';
                    document.getElementById('editPacienteResponsavelNome').value = selectedPaciente.nome_responsavel || '';
                    document.getElementById('editPacienteResponsavelCPF').value = selectedPaciente.cpf_responsavel || '';

                    bootstrap.Modal.getInstance(document.getElementById('confirmEditModal'))?.hide();
                    
                    new bootstrap.Modal(document.getElementById('editPacienteModal')).show();
            }

            function enviarEdicao(e) {
                e.preventDefault();

                const dtNascInput = document.getElementById("editPacienteDTNASC");
                const responsavelNome = document.getElementById("editPacienteResponsavelNome");
                const responsavelCPF = document.getElementById("editPacienteResponsavelCPF");

                function calcularIdade(dataNasc) {
                    const hoje = new Date();
                    const nascimento = new Date(dataNasc);
                    let idade = hoje.getFullYear() - nascimento.getFullYear();
                    const mes = hoje.getMonth() - nascimento.getMonth();
                    if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
                        idade--;
                    }
                    return idade;
                }

                const dataNasc = dtNascInput.value;
                if (dataNasc) {
                    const idade = calcularIdade(dataNasc);
                    if (idade < 18) {
                        if (!responsavelNome.value.trim() || !responsavelCPF.value.trim()) {
                            alert("Paciente menor de idade: é obrigatório informar Nome e CPF do responsável.");
                            return;
                        }
                    }
                }

                if (!selectedPaciente || !selectedPaciente.id) {
                    alert('Paciente não selecionado.');
                    return;
                }

                const cpfLimpo = document.getElementById('editPacienteCPF').value.replace(/[^\d]/g, ''); 

                const dados = {
                    nome: document.getElementById('editPacienteNome').value,
                    cpf: cpfLimpo,
                    status: document.getElementById('editPacienteStatus').value ?? '-',
                    dt_nasc: document.getElementById('editPacienteDTNASC').value,
                    sexo: document.getElementById('editPacienteSEXO').value,
                    endereco: document.getElementById('editPacienteENDERECO').value,
                    num: document.getElementById('editPacienteNUM').value,
                    complemento: document.getElementById('editPacienteCOMPLEMENTO').value,
                    bairro: document.getElementById('editPacienteBAIRRO').value,
                    uf: document.getElementById('editPacienteUF').value,
                    cep: document.getElementById('editPacienteCEP').value,
                    celular: document.getElementById('editPacienteCELULAR').value,
                    email: document.getElementById('editPacienteEMAIL').value,
                    municipio: document.getElementById('editPacienteMUNICIPIO').value,
                    nome_responsavel: responsavelNome.value,
                    cpf_responsavel : responsavelCPF.value,
                };

                fetch(`editar-paciente/${selectedPaciente.id}`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                    },
                    body: JSON.stringify(dados),
                })
                .then(async response => { 
                    if (!response.ok) { 
                        const errorData = await response.json(); 
                        throw errorData; 
                    } 
                    return response.json(); 
                })
                .then(data => { 
                    bootstrap.Modal.getInstance(document.getElementById('editPacienteModal'))?.hide(); 
                    location.reload(); 
                })
                .catch(error => { 
                    console.error("Erros de validação:", error.errors); 
                    alert(Object.values(error.errors).join("\n")); 
                });
            }

            function enviarExclusao(e) {
                e.preventDefault();
                if (!selectedPaciente || !selectedPaciente.id) return;
                fetch(`/psicologia/excluir-paciente/${selectedPaciente.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                })
                .then(response => { if (!response.ok) throw new Error('Erro ao inativar paciente - Paciente com agendamento vinculado'); return response.json(); })
                .then(data => { bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'))?.hide(); buscarPacientes(); })
                .catch(error => { console.error(error); alert(error.message); });
            }

            function enviarAtivacao(e) {
                e.preventDefault();
                if (!selectedPaciente || !selectedPaciente.id) return;
                fetch(`/psicologia/paciente/${selectedPaciente.id}/ativar`, {
                    method: 'GET',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                })
                .then(response => { if (!response.ok) throw new Error('Erro ao ativar paciente'); return response.json(); })
                .then(data => { bootstrap.Modal.getInstance(document.getElementById('editPacienteModal'))?.hide(); location.reload(); })
                .catch(error => { console.error(error); });
            }

            if (searchForm) { searchForm.addEventListener('submit', e => { e.preventDefault(); buscarPacientes(); }); }
            if (limiteSelect) { limiteSelect.addEventListener('change', () => { limiteRegistros = parseInt(limiteSelect.value) || limiteRegistros; buscarPacientes(); }); }
            const confirmEditBtn = document.getElementById('confirm-edit-btn');
            if (confirmEditBtn) confirmEditBtn.addEventListener('click', abrirModalEdicao);
            const editForm = document.getElementById('editPacienteForm');
            if (editForm) editForm.addEventListener('submit', enviarEdicao);
            const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
            if (confirmDeleteBtn) confirmDeleteBtn.addEventListener('click', enviarExclusao);
            const confirmAtivarBtn = document.getElementById('confirm-ativar-btn');
            if (confirmAtivarBtn) confirmAtivarBtn.addEventListener('click', enviarAtivacao);
            buscarPacientes();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const initializeFlatpickr = () => {
                const inputEdit = document.querySelector("#editPacienteDTNASC");
                if (inputEdit._flatpickr) inputEdit._flatpickr.destroy();
                flatpickr(inputEdit, { dateFormat: "Y-m-d", altInput: true, altFormat: "d/m/Y", allowInput: true, maxDate: "today", locale: "pt" });
                const inputSearch = document.querySelector("#DT_NASC_PACIENTE-input");
                if (inputSearch._flatpickr) inputSearch._flatpickr.destroy();
                flatpickr(inputSearch, { dateFormat: "Y-m-d", altInput: true, altFormat: "d/m/Y", allowInput: true, maxDate: "today", locale: "pt" });
            };
            const editPacienteModal = document.getElementById('editPacienteModal');
            editPacienteModal.addEventListener('shown.bs.modal', initializeFlatpickr);
            initializeFlatpickr();
        });
    </script>

    <script>
        document.addEventListener('click', function (event) {
        const btn = event.target.closest('.historico-btn');
        if (btn) {
            const pacienteId = btn.getAttribute('data-id');
            const nomePaciente = btn.getAttribute('data-nome');
                document.getElementById('nomePacienteHistorico').textContent = nomePaciente;
                const tbody = document.getElementById('historicoAgendamentosBody');
                tbody.innerHTML = `<tr><td colspan="5" class="text-center">Carregando...</td></tr>`;
                fetch(`/psicologia/agendamentos/paciente/${pacienteId}`)
                    .then(res => res.json())
                    .then(agendamentos => {
                        if (agendamentos.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="5" class="text-center">Nenhum agendamento encontrado.</td></tr>`;
                            return;
                        }
                        tbody.innerHTML = '';
                        agendamentos.forEach(ag => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td data-label="Data">${formatarDataBR(ag.DT_AGEND)}</td>
                                <td data-label="Início">${ag.HR_AGEND_INI ? ag.HR_AGEND_INI.substring(0, 8) : '-'}</td>
                                <td data-label="Fim">${ag.HR_AGEND_FIN ? ag.HR_AGEND_FIN.substring(0, 8) : '-'}</td>
                                <td data-label="Serviço">${ag.servico?.SERVICO_CLINICA_DESC ?? '-'}</td>
                                <td data-label="Status">${ag.STATUS_AGEND ?? '-'}</td>
                            `;
                            tbody.appendChild(row);
                        });
                    })
                    .catch(error => {
                        console.error(error);
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Erro ao carregar dados.</td></tr>`;
                    });
                const modal = new bootstrap.Modal(document.getElementById('historicoPacienteModal'));
                modal.show();
            }
        });
    </script>

    <script>
        document.getElementById('btnCleanFilters').addEventListener('click', function () {
            const form = document.getElementById('search-form');
            form.querySelectorAll('input').forEach(input => { input.value = ''; });
            form.querySelectorAll('select').forEach(select => { select.selectedIndex = 0; });
        })
    </script>

    <script>
        document.getElementById('editPacienteCPF').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '').slice(0, 11);
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            }
            e.target.value = value;
        });
    </script>

    <!-- VALIDA PREENCHIMENTO DE DADOS DO RESPONSÁVEL CASO ELE SEJA MENOR DE IDADE NO MODAL DE EDIT -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById("editPacienteForm");
            const dtNascInput = document.getElementById("editPacienteDTNASC");
            const responsavelNome = document.getElementById("editPacienteResponsavelNome");
            const responsavelCPF = document.getElementById("editPacienteResponsavelCPF");

            function calcularIdade(dataNasc) {
                const hoje = new Date();
                const nascimento = new Date(dataNasc);
                let idade = hoje.getFullYear() - nascimento.getFullYear();
                const mes = hoje.getMonth() - nascimento.getMonth();

                if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
                    idade--;
                }
                return idade;
            }

            form.addEventListener("submit", (e) => {
                const dataNasc = dtNascInput.value;

                if (dataNasc) {
                    const idade = calcularIdade(dataNasc);

                    if (idade < 18) {
                        if (!responsavelNome.value.trim() || !responsavelCPF.value.trim()) {
                            e.preventDefault();
                            alert("Paciente menor de idade: é obrigatório informar Nome e CPF do responsável.");
                            return;
                        }
                    }
                }
            });

        });

    </script>
</body>
</html>