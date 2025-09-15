<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastro de Serviço</title>

    <link rel="icon" type="image/png" href="/favicon_faesa.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- TOM SELECT -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            font-family: "Montserrat", sans-serif;
        }
        .shadow-dark {
            box-shadow: 0 0.75rem 1.25rem rgba(0,0,0,0.4) !important;
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
        .modal-body {
           max-height: 75vh;
           overflow-y: auto;
        }
    </style>
</head>

<body class="bg-body-secondary">

    <!-- NAVBAR COMPONENT -->
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
    
    <div id="modal-alert-container" class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1056;"></div>

    <div class="container ms-3 me-3 mw-100">
        <div class="row">
            <x-page-title>
            </x-page-title>
            <div class="col-12 shadow-lg shadow-dark p-4 bg-body-tertiary rounded">
                
                <form class="needs-validation" action="{{ route('criarServico-Psicologia') }}" method="POST" novalidate id="form-criar-servico">
                    @csrf
                    <input type="hidden" name="ID_CLINICA" value="1">
                    
                    <div class="row g-3">

                        <!-- NOME DO SERVIÇO -->
                        <div class="col-md-6">
                            <label for="nome-servico" class="form-label">Nome do Serviço</label>
                            <input type="text" id="nome-servico" name="SERVICO_CLINICA_DESC" class="form-control" value="{{ old('SERVICO_CLINICA_DESC', request('SERVICO_CLINICA_DESC')) }}" required>
                        </div>
                        
                        <!-- DISCIPLINA DO SERVIÇO -->
                        <div class="col-md-6">
                            <label for="disciplina-servico" class="form-label">Disciplina</label>
                            <select name="DISCIPLINA" id="disciplina-servico" class="form-select">
                                <option value="" selected>Carregando...</option>
                            </select>
                        </div>

                        <!-- VALOR DO SERVIÇO -->
                        <div class="col-md-3">
                            <label for="valor-servico" class="form-label">Valor do Serviço</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" id="valor-servico" name="VALOR_SERVICO" class="form-control" placeholder="0,00" value="{{ old('VALOR_SERVICO') }}">
                            </div>
                        </div>

                        <!-- RECORRÊNCIA DO SERVIÇO -->
                        <div class="col-md-3">
                            <label for="tempo_recorrencia_meses" class="form-label">Recorrência (meses)</label>
                            <input type="number" min="0" step="1" name="TEMPO_RECORRENCIA_MESES" id="tempo_recorrencia_meses" class="form-control" placeholder="Ex: 6" value="{{ old('TEMPO_RECORRENCIA_MESES') }}">
                        </div>

                        <!-- OBSERVAÇÕES DO SERVIÇO -->
                        <div class="col-12">
                            <label for="observacao-servico" class="form-label">Observações</label>
                            <textarea id="observacao-servico" name="OBSERVACAO" class="form-control">{{ old('OBSERVACAO') }}</textarea>
                        </div>

                        <div class="col-12 text-end">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-2"></i>Salvar Serviço</button>
                        </div>
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center mb-4">
                    <h2 class="fs-4 mb-0">Consulta e Edição de Serviços</h2>
                </div>
                <div class="mb-3">
                    <input type="search" id="search-servico" class="form-control" placeholder="Buscar serviço por nome..." />
                </div>
                <div class="table-responsive border rounded" style="max-height: 45vh; overflow-y: auto;">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th>Descrição</th>
                                <th>Disciplina</th>
                                <th>Valor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="servicos-tbody">
                            <tr><td colspan="4" class="text-center">Carregando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    
    <div class="modal fade" id="editarServicoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form-editar-servico" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Serviço</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-servico-id" name="ID_SERVICO_CLINICA" />
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" id="edit-servico-desc" name="SERVICO_CLINICA_DESC" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Disciplina</label>
                            <select name="DISCIPLINA" id="edit-servico-disc" class="form-select"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor do Serviço</label>
                            <input type="text" id="edit-valor-servico" name="VALOR_SERVICO" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tempo de recorrência (meses)</label>
                            <input type="number" min="0" step="1" id="edit-tempo-recorrencia-meses" name="TEMPO_RECORRENCIA_MESES" class="form-control" placeholder="Ex: 6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea id="edit-observacao-servico" name="OBSERVACAO" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-danger" id="btn-deletar-servico"><i class="bi bi-trash"></i> Excluir</button>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TOM SELECT -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.js"></script>

    <!-- FUNÇÕES JAVASCRIPT -->
    <script>
        // === FUNÇÕES ===
        function showModalAlert(message, type = 'danger') {
            const container = document.getElementById('modal-alert-container');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show m-3`;
            alert.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            container.innerHTML = '';
            container.appendChild(alert);

            setTimeout(() => alert.classList.remove('show'), 4000);
        }

        function formatarValor(valor) {
            const valorNumerico = parseFloat(valor || 0);
            return valorNumerico.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }

        window.addEventListener('DOMContentLoaded', () => {
            // === CONSTANTES E VARIÁVEIS GLOBAIS ===
            const servicosTbody = document.getElementById('servicos-tbody');
            const searchInput = document.getElementById('search-servico');
            const formEditarServico = document.getElementById('form-editar-servico');
            const editarServicoModalEl = document.getElementById('editarServicoModal');
            const editarServicoModal = new bootstrap.Modal(editarServicoModalEl);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let disciplinasCache = null;

            // === INSTANCIA DO TOM SELECT ===
            let tomSelectInstances = {};

            // === FUNÇÕES AUXILIARES ===
            async function carregarDisciplinas(selectElement, valorSelecionado = null) {
                const selectId = selectElement.id;

                if (!disciplinasCache) {
                    try {
                        const response = await fetch('/psicologia/disciplinas-psicologia');
                        if (!response.ok) throw new Error('Erro ao buscar disciplinas');
                        disciplinasCache = await response.json();
                    } catch (error) {
                        console.error(error);
                        selectElement.innerHTML = '<option value="">Erro ao carregar</option>';
                        return;
                    }
                }
                
                // Verifica se já existe uma instância do Tom Select para este elemento
                let tomSelect = tomSelectInstances[selectId];

                if (tomSelect) {
                    // Se já existe, limpa as opções e o valor atual
                    tomSelect.clear();
                    tomSelect.clearOptions();
                }

                // Adiciona as opções formatadas para o Tom Select
                const options = disciplinasCache.map(d => ({
                    value: d.DISCIPLINA,
                    text: `${d.DISCIPLINA} - ${d.NOME}`
                }));
                
                if (!tomSelect) {
                    // Se não existe, cria uma nova instância do Tom Select
                    tomSelect = new TomSelect(selectElement, {
                        options: options,
                        placeholder: 'Selecione ou pesquise...',
                        create: false,
                        sortField: {
                            field: "text",
                            direction: "asc"
                        }
                    });
                    tomSelectInstances[selectId] = tomSelect;
                } else {
                    // Se a instância já existia, apenas adiciona as novas opções
                    tomSelect.addOptions(options);
                }
                
                // Define o valor selecionado, se houver
                if (valorSelecionado) {
                    tomSelect.setValue(valorSelecionado);
                }
            }

            function ativarEventosTabela() {
                // Ativar Modal de Edição
                document.querySelectorAll('.btn-editar').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const servico = JSON.parse(btn.dataset.servico);
                        
                        formEditarServico.querySelector('#edit-servico-id').value = servico.ID_SERVICO_CLINICA;
                        formEditarServico.querySelector('#edit-servico-desc').value = servico.SERVICO_CLINICA_DESC;
                        formEditarServico.querySelector('#edit-valor-servico').value = (servico.VALOR_SERVICO || '').toString().replace('.', ',');
                        formEditarServico.querySelector('#edit-tempo-recorrencia-meses').value = servico.TEMPO_RECORRENCIA_MESES || '';
                        formEditarServico.querySelector('#edit-observacao-servico').value = servico.OBSERVACAO || '';

                        // O ID aqui precisa ser o do select DENTRO do modal
                        const selectDisc = formEditarServico.querySelector('#edit-servico-disc'); 
                        await carregarDisciplinas(selectDisc, servico.DISCIPLINA);
                        
                        editarServicoModal.show();
                    });
                });
            }

            function carregarServicos(search = '') {
                servicosTbody.innerHTML = `<tr><td colspan="4" class="text-center">Carregando...</td></tr>`;

                fetch(`/psicologia/servicos?search=${encodeURIComponent(search)}`)
                    .then(res => res.json())
                    .then(servicos => {
                        servicosTbody.innerHTML = '';

                        if (servicos.length === 0) {
                            servicosTbody.innerHTML = `<tr><td colspan="4" class="text-center">Nenhum serviço encontrado.</td></tr>`;
                            return;
                        }

                        servicos.forEach(s => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${s.SERVICO_CLINICA_DESC}</td>
                                <td>${s.DISCIPLINA || '-'}</td>
                                <td>${formatarValor(s.VALOR_SERVICO) || '-'}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-editar" title="Editar" data-servico='${JSON.stringify(s)}'>
                                        <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Editar</span>
                                    </button>
                                </td>
                            `;
                            servicosTbody.appendChild(tr);
                        });
                        ativarEventosTabela();
                    })
                    .catch(() => {
                        servicosTbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Erro ao carregar serviços.</td></tr>`;
                    });
            }

            // === EVENTOS ===
            searchInput.addEventListener('input', () => carregarServicos(searchInput.value));

            formEditarServico.addEventListener('submit', e => {
                e.preventDefault();
                const id = formEditarServico.querySelector('#edit-servico-id').value;
                const formData = new FormData(formEditarServico);
                const data = Object.fromEntries(formData.entries());
                
                if (data.VALOR_SERVICO) {
                    data.VALOR_SERVICO = data.VALOR_SERVICO.replace(',', '.');
                }

                fetch(`/psicologia/servicos/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(data)
                })
                .then(res => res.json().then(body => ({ ok: res.ok, body })))
                .then(({ ok, body }) => {
                    if (!ok) throw new Error(body.message || 'Erro ao salvar.');
                    editarServicoModal.hide();

                    // MOSTRA ALERTA DE SUCESSO
                    showModalAlert(body.message || 'Serviço atualizado com sucesso!', 'success');

                    // Atualiza tabela sem reload
                    carregarServicos(searchInput.value);
                })
                .catch(err => showModalAlert(err.message));
            });

            document.getElementById('btn-deletar-servico').addEventListener('click', () => {
                if (!confirm('Tem certeza que deseja excluir este serviço? Esta ação não pode ser desfeita.')) return;
                const id = formEditarServico.querySelector('#edit-servico-id').value;
                
                fetch(`/psicologia/servicos/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(res => res.json().then(body => ({ ok: res.ok, body })))
                .then(({ ok, body }) => {
                    if (!ok) throw new Error(body.message || 'Erro ao excluir.');
                    editarServicoModal.hide();

                    // MOSTRA ALERTA DE SUCESSO
                    showModalAlert(body.message || 'Serviço excluído com sucesso!', 'success');

                    // Atualiza tabela sem reload
                    carregarServicos(searchInput.value);
                })
                .catch(err => showModalAlert(err.message));
            });

            // === INICIALIZAÇÃO ===
            carregarServicos();
            carregarDisciplinas(document.getElementById('disciplina-servico'));
        });

        function showModalAlert(message, type = 'danger') {const container = document.getElementById('modal-alert-container');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show m-3`;
            alert.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            container.innerHTML = '';
            container.appendChild(alert);
            setTimeout(() => alert.classList.remove('show'), 4000);
        }

        function formatarValor(valor) {
            const valorNumerico = parseFloat(valor || 0);
            return valorNumerico.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }

    </script>
    
    <!-- FUNÇÃO DE FORMATAÇÃO DE INPUT -->
    <script>
        function formatarInputValor(inputElement) {
            inputElement.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                value = (parseInt(value) / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                if (value === 'NaN') value = '';
                e.target.value = value;
            });
        }
        formatarInputValor(document.getElementById('valor-servico'));
        formatarInputValor(document.getElementById('edit-valor-servico'));
    </script>

</body>
</html>