<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastro de Horários</title>

    <link rel="icon" type="image/png" href="/favicon_faesa.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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

    <!-- COMPONENT DA NAVBAR -->
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
    

    <!-- MODAL DE EDIÇÃO DE HORÁRIOS -->
    <div class="modal fade" id="editarHorarioModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">
                
                <form id="form-editar-horario" class="needs-validation">

                    <div class="modal-header">
                        <h5 class="modal-title">Editar Horário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- ID DO HORÁRIO CADASTRADO -->
                        <input type="hidden" id="edit-horario-id" name="ID_HORARIO" />
                        
                        <!-- DESCRIÇÃO -->
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" id="edit-horario-desc" name="DESCRICAO_HORARIO" class="form-control" required />
                        </div>
                        
                        <!-- TIPO DO HORÁRIO -->
                        <div class="mb-3">
                            <label class="form-label">Tipo de Horário</label>
                            <select id="edit-tipo-horario" name="BLOQUEADO" class="form-select" required>
                                <option value="N">Horário de Atendimento</option>
                                <option value="S">Horário Bloqueado</option>
                            </select>
                        </div>

                        <div class="row g-2">

                            <!-- DATA INICIAL -->
                            <div class="col-6 mb-3">
                                <label class="form-label">Data Inicial</label>
                                <input type="text" id="edit-data-horario-inicial" name="DATA_HORARIO_INICIAL" class="form-control" required />
                            </div>
                            
                            <!-- DATA FINAL -->
                            <div class="col-6 mb-3">
                                <label class="form-label">Data Final</label>
                                <input type="text" id="edit-data-horario-final" name="DATA_HORARIO_FINAL" class="form-control" required />
                            </div>
                            
                            <!-- HORÁRIO INICIAL -->
                            <div class="col-6 mb-3">
                                <label class="form-label">Horário Inicial</label>
                                <input type="text" id="edit-hr-horario-inicial" name="HR_HORARIO_INICIAL" class="form-control" required />
                            </div>

                            <!-- HORÁRIO FINAL -->
                            <div class="col-6 mb-3">
                                <label class="form-label">Horário Final</label>
                                <input type="text" id="edit-hr-horario-final" name="HR_HORARIO_FINAL" class="form-control" required />
                            </div>
                            
                        </div>
                        
                        <!-- OBSERVAÇÃO -->
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea id="edit-observacao" name="OBSERVACAO" class="form-control" rows="3">{{ old('OBSERVACAO') }}</textarea>
                        </div>

                    </div>
                    
                    <div class="modal-footer d-flex justify-content-between">

                        <!-- EXCLUR -->
                        <button type="button" class="btn btn-danger" id="btn-deletar-horario"><i class="bi bi-trash"></i> Excluir</button>
                        
                        <!-- BUTTONS -->
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                        
                    </div>
                    
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // === CONSTANTES E VARIÁVEIS GLOBAIS ===
            const horariosTbody = document.getElementById('horarios-tbody');
            const searchInput = document.getElementById('search-horario');
            const formEditarHorario = document.getElementById('form-editar-horario');
            const editarHorarioModalEl = document.getElementById('editarHorarioModal');
            const editarHorarioModal = new bootstrap.Modal(editarHorarioModalEl);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // === FUNÇÕES ===
            function showModalAlert(message, type = 'danger') {
                const container = document.getElementById('modal-alert-container');
                const alert = document.createElement('div');
                alert.className = `alert alert-${type} alert-dismissible fade show m-3`;
                alert.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                container.innerHTML = ''; // Limpa alertas anteriores
                container.appendChild(alert);
                setTimeout(() => alert.classList.remove('show'), 4000);
            }

            function ativarEventosTabela() {
                document.querySelectorAll('.btn-editar').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const horario = JSON.parse(btn.dataset.horario);
                        
                        formEditarHorario.querySelector('#edit-horario-id').value = horario.ID_HORARIO;
                        formEditarHorario.querySelector('#edit-horario-desc').value = horario.DESCRICAO_HORARIO;
                        formEditarHorario.querySelector('#edit-tipo-horario').value = horario.BLOQUEADO;
                        formEditarHorario.querySelector('#edit-observacao').value = horario.OBSERVACAO || '';

                        // Campos de data e hora
                        flatpickrInstanceEditDataIni.setDate(horario.DATA_HORARIO_INICIAL, true);
                        flatpickrInstanceEditDataFin.setDate(horario.DATA_HORARIO_FINAL, true);
                        flatpickrInstanceEditHoraIni.setDate(horario.HR_HORARIO_INICIAL, true);
                        flatpickrInstanceEditHoraFin.setDate(horario.HR_HORARIO_FINAL, true);
                        
                        editarHorarioModal.show();
                    });
                });
            }

            function carregarHorarios(search = '') {
                horariosTbody.innerHTML = `<tr><td colspan="3" class="text-center">Carregando...</td></tr>`;
                fetch(`/psicologia/horarios/listar?search=${encodeURIComponent(search)}`)
                    .then(res => res.json())
                    .then(horarios => {
                        horariosTbody.innerHTML = '';
                        if (horarios.length === 0) {
                            horariosTbody.innerHTML = `<tr><td colspan="3" class="text-center">Nenhum horário encontrado.</td></tr>`;
                            return;
                        }
                        horarios.forEach(h => {
                            const tipoBadge = h.BLOQUEADO === 'S' 
                                ? `<span class="badge bg-danger">Bloqueio</span>` 
                                : `<span class="badge bg-info">Atendimento</span>`;

                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${h.DESCRICAO_HORARIO}</td>
                                <td>${tipoBadge}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-editar" title="Editar" data-horario='${JSON.stringify(h)}'>
                                        <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Editar</span>
                                    </button>
                                </td>
                            `;
                            horariosTbody.appendChild(tr);
                        });
                        ativarEventosTabela();
                    })
                    .catch(() => {
                        horariosTbody.innerHTML = `<tr><td colspan="3" class="text-center text-danger">Erro ao carregar horários.</td></tr>`;
                    });
            }

            // === EVENTOS ===
            searchInput.addEventListener('input', () => carregarHorarios(searchInput.value));

            formEditarHorario.addEventListener('submit', e => {
                
                e.preventDefault();
                
                const id = formEditarHorario.querySelector('#edit-horario-id').value;
                const formData = new FormData(formEditarHorario);
                const data = Object.fromEntries(formData.entries());
                
                fetch(`/psicologia/horarios/atualizar/${id}`, {
                    method: 'PUT',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => {
                    if (!res.ok) return res.json().then(body => { throw body; });
                    return res.json();
                })
                .then(body => {
                    if (body.message) {
                        editarHorarioModal.hide();
                        showModalAlert(body.message, 'success');
                        carregarHorarios();
                    }
                })
                .catch(err => {
                    if (err.errors) {
                        const mensagens = Object.values(err.errors).flat().join('<br>');
                        showModalAlert(mensagens, 'danger');
                    } else {
                        showModalAlert(err.message || 'Ocorreu um erro inesperado.', 'danger');
                    }
                });
            });
            document.getElementById('btn-deletar-horario').addEventListener('click', () => {
                if (!confirm('Tem certeza que deseja excluir este horário? Esta ação não pode ser desfeita.')) return;
                const id = formEditarHorario.querySelector('#edit-horario-id').value;
                
                fetch(`/psicologia/horarios/deletar/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                })
                .then(res => res.json().then(body => ({ ok: res.ok, body })))
                .then(({ ok, body }) => {
                    if (!ok) throw new Error(body.message || 'Erro ao excluir.');
                    editarHorarioModal.hide();
                    window.location.reload();
                })
                .catch(err => showModalAlert(err.message));
            });

            // === INICIALIZAÇÃO ===
            carregarHorarios();
        });
    </script>
    
    <script>
        flatpickr.localize(flatpickr.l10ns.pt);
        const configData = { dateFormat: "Y-m-d", altInput: true, altFormat: "d/m/Y", locale: "pt" };
        const configHora = { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true };

        // Formulário de Criação
        flatpickr("#DATA_HORARIO_INICIAL", configData);
        flatpickr("#DATA_HORARIO_FINAL", configData);
        flatpickr("#HR_HORARIO_INICIAL", configHora);
        flatpickr("#HR_HORARIO_FINAL", configHora);

        // Formulário de Edição (Modal)
        const flatpickrInstanceEditDataIni = flatpickr("#edit-data-horario-inicial", configData);
        const flatpickrInstanceEditDataFin = flatpickr("#edit-data-horario-final", configData);
        const flatpickrInstanceEditHoraIni = flatpickr("#edit-hr-horario-inicial", configHora);
        const flatpickrInstanceEditHoraFin = flatpickr("#edit-hr-horario-final", configHora);
    </script>
</body>
</html>