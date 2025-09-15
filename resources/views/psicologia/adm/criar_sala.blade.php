<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastro de Sala</title>

    <link rel="icon" type="image/png" href="/favicon_faesa.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- TOM SELECT -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

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
                
                <form class="needs-validation" action="{{ route('criarSala-Psicologia') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="ID_CLINICA" value="1">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nome-sala" class="form-label">Descrição da Sala</label>
                            <input type="text" id="nome-sala" name="DESCRICAO" class="form-control" value="{{ old('DESCRICAO', request('DESCRICAO')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="disciplina-sala" class="form-label">Disciplina</label>
                            <select name="DISCIPLINA" id="disciplina-sala" class="form-select">
                                <option value="" selected>Carregando...</option>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle me-2"></i>Salvar Sala</button>
                        </div>
                    </div>
                </form>

                <hr class="my-4">

                <div class="text-center mb-4">
                    <h2 class="fs-4 mb-0">Consulta e Edição de Salas</h2>
                </div>
                <div class="mb-3">
                    <input type="search" id="search-sala" class="form-control" placeholder="Buscar sala por nome..." />
                </div>
                <div class="table-responsive border rounded" style="max-height: 50vh; overflow-y: auto;">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th>Descrição</th>
                                <th>Disciplina</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="salas-tbody">
                            <tr><td colspan="4" class="text-center">Carregando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="editarSalaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form-editar-sala" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Sala</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-sala-id" name="ID_SALA_CLINICA" />
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" id="edit-sala-desc" name="DESCRICAO" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Disciplina</label>
                            <select name="DISCIPLINA" id="edit-sala-disc" class="form-select"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select id="edit-sala-status" name="ATIVO" class="form-select" required>
                                <option value="S">Ativo</option>
                                <option value="N">Inativo</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-danger" id="btn-deletar-sala"><i class="bi bi-trash"></i> Excluir</button>
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

        window.addEventListener('DOMContentLoaded', () => {
            // === CONSTANTES E VARIÁVEIS GLOBAIS ===
            const salasTbody = document.getElementById('salas-tbody');
            const searchInput = document.getElementById('search-sala');
            const formEditarSala = document.getElementById('form-editar-sala');
            const editarSalaModalEl = document.getElementById('editarSalaModal');
            const editarSalaModal = new bootstrap.Modal(editarSalaModalEl);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let disciplinasCache = null;

            // === INSTANCIA DO TOM SELECT ===
            let tomSelectInstances = {};

            // === FUNÇÕES AUXILIARES ===
            async function carregarDisciplinas(selectElement, valorSelecionado = null) {
                // Pega o ID do elemento para usar como chave da instância
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
                document.querySelectorAll('.btn-editar').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const sala = JSON.parse(btn.dataset.sala);
                        
                        formEditarSala.querySelector('#edit-sala-id').value = sala.ID_SALA_CLINICA;
                        formEditarSala.querySelector('#edit-sala-desc').value = sala.DESCRICAO;
                        formEditarSala.querySelector('#edit-sala-status').value = sala.ATIVO;

                        const selectDisc = formEditarSala.querySelector('#edit-sala-disc');
                        await carregarDisciplinas(selectDisc, sala.DISCIPLINA);
                        
                        editarSalaModal.show();
                    });
                });
            }

            async function carregarSalas(search = ''){
                salasTbody.innerHTML = `<tr><td colspan="4" class="text-center">Carregando...</td></tr>`;

                try {
                    const response = await fetch(`/psicologia/salas/listar?search=${encodeURIComponent(search)}`);
                    const salas = await response.json();

                    if (salas.length === 0) {
                        salasTbody.innerHTML = `<tr><td colspan="4" class="text-center">Nenhuma sala encontrada.</td></tr>`;
                        return;
                    }

                    salasTbody.innerHTML = '';
                    salas.forEach((s, index) => {
                        const statusBadge = s.ATIVO === 'S' 
                            ? `<span class="badge bg-success">Ativo</span>` 
                            : `<span class="badge bg-danger">Inativo</span>`;

                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${s.DESCRICAO}</td>
                            <td>${s.DISCIPLINA || ''}</td>
                            <td>${statusBadge}</td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-editar" title="Editar" data-sala='${JSON.stringify(s)}'>
                                    <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Editar</span>
                                </button>
                            </td>
                        `;
                        salasTbody.appendChild(tr);
                    });

                    ativarEventosTabela();

                } catch (error) {
                    console.error("Erro ao carregar salas ou disciplinas:", error);
                    salasTbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Erro ao carregar salas.</td></tr>`;
                }
            }

            // === EVENTOS ===
            searchInput.addEventListener('input', () => carregarSalas(searchInput.value));

            formEditarSala.addEventListener('submit', e => {
                
                e.preventDefault();

                const id = formEditarSala.querySelector('#edit-sala-id').value;
                const formData = new FormData(formEditarSala);
                const data = Object.fromEntries(formData.entries());

                fetch(`/psicologia/salas/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(data)
                })
                .then(res => res.json().then(body => ({ ok: res.ok, body })))
               .then(({ ok, body }) => {
                    if (!ok) throw new Error(body.message || 'Erro ao salvar.');

                    // Fecha o modal
                    editarSalaModal.hide();

                    // Mostra a mensagem de sucesso
                    showModalAlert(body.message, 'success');

                    // Atualiza a tabela sem recarregar ou recarrega após 2 segundos
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                })
                .catch(err => showModalAlert(err.message));
            });
            
            document.getElementById('btn-deletar-sala').addEventListener('click', () => {
                if (!confirm('Tem certeza que deseja excluir esta sala? Esta ação não pode ser desfeita.')) return;

                const id = formEditarSala.querySelector('#edit-sala-id').value;
                
                fetch(`/psicologia/salas/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(res => {
                    return res.json().then(body => ({ ok: res.ok, status: res.status, body }));
                })
                .then(({ ok, body }) => {
                    if (!ok) {
                        throw new Error(body.message || 'Ocorreu um erro ao processar a solicitação.');
                    }

                    editarSalaModal.hide();

                    showModalAlert(body.message, 'success'); 
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                })
                .catch(err => {
                    showModalAlert(err.message, 'danger');
                });
            });

            // === INICIALIZAÇÃO ===
            carregarSalas();
            carregarDisciplinas(document.getElementById('disciplina-sala'));
        });
    </script>

</body>
</html>