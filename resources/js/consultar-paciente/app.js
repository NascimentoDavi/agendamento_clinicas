// pacientes.js

import 'bootstrap';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import { Portuguese } from 'flatpickr/dist/l10n/pt.js';

flatpickr.localize(Portuguese);

document.addEventListener('DOMContentLoaded', () => {

    // ======== FUNÇÕES AUXILIARES ========
    function formatarDataBR(dateStr) {
        if (!dateStr) return '-';
        const cleanedDate = dateStr.split('T')[0];
        const [year, month, day] = cleanedDate.split('-');
        return `${day}/${month}/${year}`;
    }

    function montarQueryParams(params) {
        return Object.entries(params)
            .filter(([_, v]) => v !== null && String(v).trim() !== '')
            .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
            .join('&');
    }

    function calcularIdade(dataNasc) {
        const hoje = new Date();
        const nascimento = new Date(dataNasc);
        let idade = hoje.getFullYear() - nascimento.getFullYear();
        const mes = hoje.getMonth() - nascimento.getMonth();
        if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) idade--;
        return idade;
    }

    // ======== SELETORES ========
    const pacientesTbody = document.getElementById('pacientes-tbody');
    const searchForm = document.getElementById('search-form');
    const limiteSelect = document.getElementById('limite-visualizacao');
    const contadorRegistros = document.getElementById('contador-registros');
    let selectedPaciente = null;
    let limiteRegistros = limiteSelect ? parseInt(limiteSelect.value) || 10 : 10;

    let sortField = null;
    let sortAsc = true;

    // ======== EVENTOS DE AÇÃO ========
    function ativarEventosEditar() {
        document.querySelectorAll('.editar-btn').forEach(button => {
            button.addEventListener('click', () => {
                selectedPaciente = {
                    id: button.dataset.id,
                    status: button.dataset.status ?? 'nada',
                    nome: button.dataset.nome,
                    cpf: button.dataset.cpf,
                    dt_nasc: button.dataset.dt_nasc,
                    sexo: button.dataset.sexo,
                    endereco: button.dataset.endereco,
                    num: button.dataset.num,
                    complemento: button.dataset.complemento,
                    bairro: button.dataset.bairro,
                    uf: button.dataset.uf,
                    cep: button.dataset.cep,
                    celular: button.dataset.celular,
                    email: button.dataset.email,
                    municipio: button.dataset.municipio,
                    nome_responsavel: button.dataset.nomeResponsavel,
                    cpf_responsavel: button.dataset.cpfResponsavel,
                };
                document.getElementById('modal-paciente-nome').textContent =
                    `Deseja editar o paciente: ${selectedPaciente.nome}?`;
                new bootstrap.Modal(document.getElementById('confirmEditModal')).show();
            });
        });
    }

    document.getElementById('confirm-edit-btn')?.addEventListener('click', () => {
        // Fecha o modal de confirmação
        const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmEditModal'));
        confirmModal.hide();

        // Preenche os campos da modal de edição
        document.getElementById('editPacienteNome').value = selectedPaciente.nome ?? '';
        document.getElementById('editPacienteCPF').value = selectedPaciente.cpf ?? '';
        document.getElementById('editPacienteDTNASC').value = selectedPaciente.dt_nasc ?? '';
        document.getElementById('editPacienteSEXO').value = selectedPaciente.sexo ?? '';
        document.getElementById('editPacienteENDERECO').value = selectedPaciente.endereco ?? '';
        document.getElementById('editPacienteNUM').value = selectedPaciente.num ?? '';
        document.getElementById('editPacienteCOMPLEMENTO').value = selectedPaciente.complemento ?? '';
        document.getElementById('editPacienteBAIRRO').value = selectedPaciente.bairro ?? '';
        document.getElementById('editPacienteUF').value = selectedPaciente.uf ?? '';
        document.getElementById('editPacienteCEP').value = selectedPaciente.cep ?? '';
        document.getElementById('editPacienteCELULAR').value = selectedPaciente.celular ?? '';
        document.getElementById('editPacienteEMAIL').value = selectedPaciente.email ?? '';
        document.getElementById('editPacienteMUNICIPIO').value = selectedPaciente.municipio ?? '';
        document.getElementById('editPacienteResponsavelNome').value = selectedPaciente.nome_responsavel ?? '';
        document.getElementById('editPacienteResponsavelCPF').value = selectedPaciente.cpf_responsavel ?? '';
        document.getElementById('editPacienteStatus').value = selectedPaciente.status ?? '';

        // Abre a modal de edição
        new bootstrap.Modal(document.getElementById('editPacienteModal')).show();

        // Inicializa o flatpickr no campo de data
        initializeFlatpickr();
    });

    function ativarEventosDeletar() {
        document.querySelectorAll('.excluir-btn').forEach(button => {
            button.addEventListener('click', () => {
                selectedPaciente = { id: button.dataset.id, nome: button.dataset.nome };
                document.getElementById('modal-delete-nome').textContent =
                    `Deseja inativar o paciente: ${selectedPaciente.nome}?`;
                new bootstrap.Modal(document.getElementById('confirmDeleteModal')).show();
            });
        });
    }

    function ativarEventosAtivar() {
        document.querySelectorAll('.ativar-btn').forEach(button => {
            button.addEventListener('click', () => {
                selectedPaciente = { id: button.dataset.id, nome: button.dataset.nome };
                document.getElementById('modal-ativar-nome').textContent =
                    `Deseja ativar o paciente: ${selectedPaciente.nome}?`;
                new bootstrap.Modal(document.getElementById('confirmAtivarModal')).show();
            });
        });
    }

    // ======== BUSCA E RENDER ========
    function buscarPacientes() {
        if (!pacientesTbody) return;

        const filtros = {
            search: document.getElementById('search-input')?.value.trim() ?? '',
            DT_NASC_PACIENTE: document.getElementById('DT_NASC_PACIENTE-input')?.value ?? '',
            STATUS: document.getElementById('status-input')?.value ?? '-',
            SEXO_PACIENTE: document.getElementById('sexo')?.value ?? '',
            FONE_PACIENTE: document.getElementById('telefone-input')?.value ?? '',
        };

        const queryString = montarQueryParams(filtros);

        fetch(`/psicologia/consultar-paciente/buscar?${queryString}`)
            .then(res => { if (!res.ok) throw new Error(`HTTP ${res.status}`); return res.json(); })
            .then(pacientes => renderPacientes(pacientes))
            .catch(err => {
                console.error(err);
                pacientesTbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Erro ao buscar pacientes.</td></tr>`;
            });
    }

    function sortPacientes(pacientes) {
        if (!sortField) return pacientes;
        return pacientes.slice().sort((a, b) => {
            let valA = a[sortField] ?? '';
            let valB = b[sortField] ?? '';
            if (sortField === 'DT_NASC_PACIENTE') {
                valA = valA ? new Date(valA) : new Date(0);
                valB = valB ? new Date(valB) : new Date(0);
            }
            if (valA < valB) return sortAsc ? -1 : 1;
            if (valA > valB) return sortAsc ? 1 : -1;
            return 0;
        });
    }

    function renderPacientes(pacientes) {
        pacientesTbody.innerHTML = '';
        if (!pacientes || pacientes.length === 0) {
            pacientesTbody.innerHTML = `<tr><td colspan="8" class="text-center">Nenhum paciente encontrado.</td></tr>`;
            contadorRegistros.innerHTML = `<span>Total: 0</span>`;
            return;
        }

        const pacientesVisiveis = pacientes.slice(0, limiteRegistros);
        contadorRegistros.innerHTML = `<span>Mostrando ${pacientesVisiveis.length} de ${pacientes.length}</span>`;

        const sorted = sortPacientes(pacientesVisiveis);

        sorted.forEach(p => {
            const row = document.createElement('tr');
            const isInativo = p.STATUS === 'Inativo';
            const btnStatus = isInativo
                ? `<button type="button" class="btn btn-sm btn-success ativar-btn" data-id="${p.ID_PACIENTE}" data-nome="${p.NOME_COMPL_PACIENTE ?? 'Paciente'}"><i class="bi bi-check2"></i></button>`
                : `<button type="button" class="btn btn-sm btn-danger excluir-btn" data-id="${p.ID_PACIENTE}" data-nome="${p.NOME_COMPL_PACIENTE ?? 'Paciente'}"><i class="bi bi-trash"></i></button>`;

            row.innerHTML = `
                <td data-label="Nome">${p.NOME_COMPL_PACIENTE}</td>
                <td data-label="CPF">${p.CPF_PACIENTE}</td>
                <td data-label="Nascimento">${p.DT_NASC_PACIENTE ? formatarDataBR(p.DT_NASC_PACIENTE) : '-'}</td>
                <td data-label="Sexo">${p.SEXO_PACIENTE ?? '-'}</td>
                <td data-label="Telefone">${p.FONE_PACIENTE ?? '-'}</td>
                <td data-label="Email">${p.E_MAIL_PACIENTE ?? '-'}</td>
                <td data-label="Status">${p.STATUS ?? '-'}</td>
                <td data-label="Ações" class="actions-cell">
                    <div class="d-flex flex-wrap justify-content-center gap-1">
                        <button type="button" class="btn btn-sm btn-warning editar-btn" 
                            data-id="${p.ID_PACIENTE}" 
                            data-status="${p.STATUS ?? '-'}" 
                            data-nome="${p.NOME_COMPL_PACIENTE ?? 'Paciente'}"
                            data-cpf="${p.CPF_PACIENTE ?? ''}"
                            data-dt_nasc="${p.DT_NASC_PACIENTE ?? ''}"
                            data-sexo="${p.SEXO_PACIENTE ?? ''}"
                            data-endereco="${p.ENDERECO ?? ''}"
                            data-num="${p.END_NUM ?? ''}"
                            data-complemento="${p.COMPLEMENTO ?? ''}"
                            data-bairro="${p.BAIRRO ?? ''}"
                            data-uf="${p.UF ?? ''}"
                            data-cep="${p.CEP ?? ''}"
                            data-celular="${p.FONE_PACIENTE ?? ''}"
                            data-email="${p.E_MAIL_PACIENTE ?? ''}"
                            data-municipio="${p.MUNICIPIO ?? ''}"
                            data-nome-responsavel="${p.NOME_RESPONSAVEL ?? ''}"
                            data-cpf-responsavel="${p.CPF_RESPONSAVEL ?? ''}">
                            <i class="bi bi-pencil"></i>
                            <button type="button" class="btn btn-sm btn-secondary historico-btn" data-id="${p.ID_PACIENTE}" data-nome="${p.NOME_COMPL_PACIENTE}">
                                <i class="bi bi-clock-history"></i>
                            </button>
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

        // Accordion mobile
        renderAccordion(sorted);

        // Redimensionamento de colunas
        const tabelaElement = document.getElementById('tabela')?.querySelector('table');
        if (tabelaElement) makeColumnsResizable(tabelaElement);
    }

    // ======== FLATPICKR ========
    function initializeFlatpickr() {
        ['#editPacienteDTNASC', '#DT_NASC_PACIENTE-input'].forEach(selector => {
            const el = document.querySelector(selector);
            if (!el) return;
            if (el._flatpickr) el._flatpickr.destroy();
            flatpickr(el, {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                allowInput: true,
                maxDate: "today",
                locale: "pt"
            });
        });
    }
    initializeFlatpickr();

    // ======== EVENTOS ========
    searchForm?.addEventListener('submit', e => { e.preventDefault(); buscarPacientes(); });
    limiteSelect?.addEventListener('change', () => { limiteRegistros = parseInt(limiteSelect.value) || limiteRegistros; buscarPacientes(); });

    document.getElementById('btnCleanFilters')?.addEventListener('click', function () {
        const form = searchForm;
        form.querySelectorAll('input').forEach(input => { input.value = ''; });
        form.querySelectorAll('select').forEach(select => { select.selectedIndex = 0; });
    });

    document.getElementById('editPacienteCPF')?.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '').slice(0, 11);
        value = value.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = value;
    });

    // ======== ORDENAR COLUNAS ========
    document.querySelectorAll('#tabela thead th[data-sort]').forEach(th => {
        th.style.cursor = 'pointer';
        th.addEventListener('click', () => {
            const field = th.dataset.sort;
            if (sortField === field) sortAsc = !sortAsc;
            else { sortField = field; sortAsc = true; }

            document.querySelectorAll('#tabela thead th[data-sort]').forEach(h => { h.innerHTML = h.textContent.trim(); });
            const icon = sortAsc ? 'bi-caret-up-fill' : 'bi-caret-down-fill';
            th.innerHTML = `${th.textContent.trim()} <i class="bi ${icon} ms-1"></i>`;

            buscarPacientes();
        });
    });

    // ======== ACORDION MOBILE ========
    function renderAccordion(pacientes) {
        const container = document.getElementById('pacientesAccordion');
        if (!container) return;
        container.innerHTML = '';
        pacientes.forEach((p, idx) => {
            const item = document.createElement('div');
            item.className = 'accordion-item mb-2';
            item.innerHTML = `
                <h2 class="accordion-header" id="heading${idx}">
                    <button class="accordion-button collapsed" type="button">
                        ${p.NOME_COMPL_PACIENTE} - ${p.CPF_PACIENTE}
                    </button>
                </h2>
                <div class="accordion-content">
                    <div class="accordion-body">
                        <ul class="list-unstyled mb-0">
                            <li><strong>Nascimento:</strong> ${formatarDataBR(p.DT_NASC_PACIENTE)}</li>
                            <li><strong>Sexo:</strong> ${p.SEXO_PACIENTE ?? '-'}</li>
                            <li><strong>Telefone:</strong> ${p.FONE_PACIENTE ?? '-'}</li>
                            <li><strong>Email:</strong> ${p.E_MAIL_PACIENTE ?? '-'}</li>
                            <li><strong>Status:</strong> ${p.STATUS ?? '-'}</li>
                        </ul>
                    </div>
                </div>
            `;
            const btn = item.querySelector('.accordion-button');
            const content = item.querySelector('.accordion-content');
            content.style.maxHeight = '0px';
            content.style.overflow = 'hidden';
            content.style.transition = 'max-height 0.3s ease';
            btn.addEventListener('click', e => {
                e.preventDefault();
                const isOpen = btn.getAttribute('aria-expanded') === 'true';
                if (isOpen) { content.style.maxHeight = '0px'; btn.classList.add('collapsed'); btn.setAttribute('aria-expanded', 'false'); }
                else { content.style.maxHeight = content.scrollHeight + 'px'; btn.classList.remove('collapsed'); btn.setAttribute('aria-expanded', 'true'); }
            });
            window.addEventListener('resize', () => { if (btn.getAttribute('aria-expanded') === 'true') content.style.maxHeight = content.scrollHeight + 'px'; });
            container.appendChild(item);
        });
    }

    // ======== REDIMENSIONAR COLUNAS ========
    function makeColumnsResizable(table) {
        if (!table) return;
        table.style.tableLayout = 'fixed';
        const ths = table.querySelectorAll('th');
        ths.forEach((th, index) => {
            const grip = document.createElement('div');
            Object.assign(grip.style, { position: 'absolute', right: '0', top: '0', height: '100%', width: '5px', cursor: 'col-resize', userSelect: 'none', zIndex: '1' });
            th.style.position = 'relative';
            th.appendChild(grip);
            let startX, startWidth;
            grip.addEventListener('mousedown', e => {
                e.preventDefault();
                startX = e.pageX;
                startWidth = th.offsetWidth;
                function onMouseMove(eMove) {
                    const newWidth = startWidth + (eMove.pageX - startX);
                    if (newWidth > 30) {
                        th.style.width = newWidth + 'px';
                        table.querySelectorAll('tr').forEach(tr => { const cell = tr.children[index]; if (cell) cell.style.width = newWidth + 'px'; });
                    }
                }
                function onMouseUp() { document.removeEventListener('mousemove', onMouseMove); document.removeEventListener('mouseup', onMouseUp); }
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            });
        });
    }

    // ======== INICIAL ========
    buscarPacientes();

    const tabela = document.getElementById('tabela')?.querySelector('table');
    makeColumnsResizable(tabela);

    const editForm = document.getElementById('editPacienteForm');
    if (editForm) editForm.addEventListener('submit', enviarEdicao);

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

    document.addEventListener('click', function (event) {
    const btn = event.target.closest('.historico-btn');
    if (btn) {
        const pacienteId = btn.getAttribute('data-id');
        const nomePaciente = btn.getAttribute('data-nome');

        // --- Seleciona TODOS os elementos do modal que vamos manipular ---
        const nomePacienteEl = document.getElementById('nomePacienteHistorico');
        const tbody = document.getElementById('historicoAgendamentosBody');
        const loadingEl = document.getElementById('historicoLoading');
        const vazioEl = document.getElementById('historicoVazio');
        const tabelaWrapperEl = document.getElementById('tabelaHistoricoWrapper');
        const historicoModal = new bootstrap.Modal(document.getElementById('historicoPacienteModal'));

        // 1. PREPARA O MODAL: Mostra o spinner e esconde o resto
        nomePacienteEl.textContent = nomePaciente;
        tbody.innerHTML = ''; // Limpa a tabela de dados anteriores
        loadingEl.classList.remove('d-none'); // MOSTRA o spinner
        vazioEl.classList.add('d-none'); // ESCONDE a mensagem de vazio
        tabelaWrapperEl.classList.add('d-none'); // ESCONDE a tabela

        historicoModal.show(); // Abre o modal já com o spinner

        // 2. BUSCA OS DADOS
        fetch(`/psicologia/agendamentos/paciente/${pacienteId}`)
            .then(res => res.json())
            .then(agendamentos => {
                // 3. ATUALIZA A TELA: Esconde o spinner e mostra o resultado
                loadingEl.classList.add('d-none'); // ESCONDE o spinner

                if (agendamentos.length === 0) {
                    // Se não veio nada, mostra a mensagem de vazio
                    vazioEl.classList.remove('d-none');
                } else {
                    // Se vieram dados, preenche a tabela
                    agendamentos.forEach(ag => {
                        const row = tbody.insertRow(); // Usar insertRow é mais eficiente
                        row.innerHTML = `
                            <td data-label="Data">${formatarDataBR(ag.DT_AGEND)}</td>
                            <td data-label="Início">${ag.HR_AGEND_INI ? ag.HR_AGEND_INI.substring(0, 5) : '-'}</td>
                            <td data-label="Fim">${ag.HR_AGEND_FIN ? ag.HR_AGEND_FIN.substring(0, 5) : '-'}</td>
                            <td data-label="Serviço">${ag.servico?.SERVICO_CLINICA_DESC ?? '-'}</td>
                            <td data-label="Status">${ag.STATUS_AGEND ?? '-'}</td>
                        `;
                    });
                    // E finalmente, MOSTRA a tabela
                    tabelaWrapperEl.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error(error);
                loadingEl.classList.add('d-none'); // Esconde o spinner
                vazioEl.classList.remove('d-none'); // Mostra a div de vazio
                // Atualiza o texto para informar sobre o erro
                vazioEl.querySelector('p').textContent = 'Erro ao carregar o histórico.';
            });
        }
    });
});
