import 'bootstrap';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import { Portuguese } from 'flatpickr/dist/l10n/pt.js';

// Localização PT-BR
flatpickr.localize(Portuguese);

document.addEventListener("DOMContentLoaded", function () {
    const agendamentosTbody = document.getElementById('agendamentos-tbody');
    const searchForm = document.getElementById('search-form');
    const limitSelect = document.getElementById('limit-select');
    const contadorRegistros = document.getElementById('contador-registros');

    let sortField = null;
    let sortAsc = true;

    // ============ FUNÇÕES ============

    function getFilters() {
        const formData = new FormData(searchForm);
        const params = new URLSearchParams(formData);
        params.append('limit', limitSelect.value);
        return params;
    }

    function sortAgendamentos(agendamentos) {
        if (!sortField) return agendamentos;

        return agendamentos.slice().sort((a, b) => {
            let valA, valB;
            switch(sortField) {
                case 'paciente':
                    valA = a.paciente?.NOME_COMPL_PACIENTE ?? '';
                    valB = b.paciente?.NOME_COMPL_PACIENTE ?? '';
                    break;
                case 'aluno':
                    valA = a.aluno?.NOME_COMPL ?? a.ID_ALUNO ?? '';
                    valB = b.aluno?.NOME_COMPL ?? b.ID_ALUNO ?? '';
                    break;
                case 'servico':
                    valA = a.servico?.SERVICO_CLINICA_DESC ?? '';
                    valB = b.servico?.SERVICO_CLINICA_DESC ?? '';
                    break;
                case 'data':
                    valA = a.DT_AGEND ? new Date(a.DT_AGEND) : new Date(0);
                    valB = b.DT_AGEND ? new Date(b.DT_AGEND) : new Date(0);
                    break;
                case 'horaIni':
                    valA = a.HR_AGEND_INI ?? '';
                    valB = b.HR_AGEND_INI ?? '';
                    break;
                case 'horaFim':
                    valA = a.HR_AGEND_FIN ?? '';
                    valB = b.HR_AGEND_FIN ?? '';
                    break;
                case 'local':
                    valA = a.LOCAL ?? '';
                    valB = b.LOCAL ?? '';
                    break;
                case 'status':
                    valA = a.STATUS_AGEND ?? '';
                    valB = b.STATUS_AGEND ?? '';
                    break;
                case 'reagendamento':
                    valA = a.ID_AGEND_REMARCADO != null ? 1 : 0;
                    valB = b.ID_AGEND_REMARCADO != null ? 1 : 0;
                    break;
                case 'valor':
                    valA = parseFloat(a.VALOR_AGEND) || 0;
                    valB = parseFloat(b.VALOR_AGEND) || 0;
                    break;
                case 'pago':
                    valA = a.STATUS_PAG === 'S' ? 1 : 0;
                    valB = b.STATUS_PAG === 'S' ? 1 : 0;
                    break;
                case 'valorPago':
                    valA = parseFloat(a.VALOR_PAG) || 0;
                    valB = parseFloat(b.VALOR_PAG) || 0;
                    break;
                default:
                    valA = '';
                    valB = '';
            }
            if (valA < valB) return sortAsc ? -1 : 1;
            if (valA > valB) return sortAsc ? 1 : -1;
            return 0;
        });
    }

    function renderAgendamentos(agendamentos) {
        const sorted = sortAgendamentos(agendamentos);

        agendamentosTbody.innerHTML = '';
        contadorRegistros.innerHTML = `<span class="text-muted">Total de registros: ${sorted.length}</span>`;

        if (sorted.length === 0) {
            agendamentosTbody.innerHTML = `
                <tr>
                    <td colspan="13" class="text-center">Nenhum agendamento encontrado para os filtros aplicados.</td>
                </tr>`;
            return;
        }

        sorted.forEach(ag => {
            const paciente = ag.paciente ? ag.paciente.NOME_COMPL_PACIENTE : '-';
            const aluno = ag.aluno ? ag.aluno.NOME_COMPL : (ag.ID_ALUNO || '-');
            const servico = ag.servico ? ag.servico.SERVICO_CLINICA_DESC : '-';
            const data = ag.DT_AGEND
                ? new Date(ag.DT_AGEND).toLocaleDateString('pt-BR', { timeZone: 'UTC' })
                : '-';
            const horaIni = ag.HR_AGEND_INI ? ag.HR_AGEND_INI.substring(0, 5) : '-';
            const horaFim = ag.HR_AGEND_FIN ? ag.HR_AGEND_FIN.substring(0, 5) : '-';
            const local = ag.LOCAL ?? '-';
            const status = ag.STATUS_AGEND || '-';
            const valor = ag.VALOR_AGEND
                ? parseFloat(ag.VALOR_AGEND).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
                : '-';
            const checkPagamento = ag.STATUS_PAG === 'S'
                ? '<span class="badge bg-success">Sim</span>'
                : '<span class="badge bg-danger">Não</span>';
            const valorPagamento = ag.VALOR_PAG
                ? parseFloat(ag.VALOR_PAG).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
                : '-';
            const reagendamento = ag.ID_AGEND_REMARCADO != null ? 'Sim' : 'Não';

            const statusMap = {
                Agendado: { color: 'text-success', icon: 'bi-calendar-check' },
                Cancelado: { color: 'text-danger', icon: 'bi-calendar-x' },
                Presente: { color: 'text-primary', icon: 'bi-check2-circle' },
                Finalizado: { color: 'text-secondary', icon: 'bi-calendar2-check-fill' },
                Remarcado: { color: 'text-warning', icon: 'bi-arrow-repeat' }
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
                <td data-label="Status" class="fw-bold ${statusInfo.color}">
                    <i class="bi ${statusInfo.icon} me-1"></i>${status}
                </td>
                <td data-label="Reagendamento?">${reagendamento}</td>
                <td data-label="Valor">${valor}</td>
                <td data-label="Pago?" class="text-md-center">${checkPagamento}</td>
                <td data-label="Valor Pago">${valorPagamento}</td>
                <td data-label="Ações" class="agendamento-actions">
                    <div class="d-flex justify-content-center align-items-start gap-1">
                        <a href="/psicologia/agendamento/${ag.ID_AGENDAMENTO}/editar" class="btn btn-sm btn-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                        <form action="/psicologia/agendamento/${ag.ID_AGENDAMENTO}" method="POST" onsubmit="return confirm('Confirma a exclusão deste agendamento?');">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            `;
            agendamentosTbody.appendChild(row);
        });

        // renderiza o accordion (mobile)
        renderAccordion(sorted);

        // chama novamente a função de redimensionamento
        const tabelaElement = document.getElementById('tabela').querySelector('table');
        makeColumnsResizable(tabelaElement);
    }

    function carregarAgendamentos() {
        const params = getFilters();
        const url = `/psicologia/get-agendamento?${params.toString()}`;

        agendamentosTbody.innerHTML = `
            <tr>
                <td colspan="13" class="text-center">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Carregando...
                </td>
            </tr>
        `;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Erro na resposta da rede.');
                return response.json();
            })
            .then(agendamentos => renderAgendamentos(agendamentos))
            .catch(error => {
                console.error('Erro ao buscar agendamentos:', error);
                agendamentosTbody.innerHTML = `
                    <tr>
                        <td colspan="13" class="text-center text-danger">
                            Falha ao carregar os dados. Tente novamente mais tarde.
                        </td>
                    </tr>`;
            });
    }

    // ============ EVENTOS ============

    searchForm.addEventListener('submit', e => { e.preventDefault(); carregarAgendamentos(); });
    limitSelect.addEventListener('change', carregarAgendamentos);

    document.getElementById('btnClearFilters').addEventListener('click', () => {
        searchForm.reset();
        flatpickrInstances.forEach(instance => instance.clear());
        carregarAgendamentos();
    });

    // Ordenação clicando no cabeçalho
    document.querySelectorAll('#tabela thead th[data-sort]').forEach(th => {
        th.style.cursor = 'pointer'; // muda o cursor para indicar que é clicável

        th.addEventListener('click', () => {
            const field = th.dataset.sort;

            // alterna direção se clicar na mesma coluna
            if (sortField === field) sortAsc = !sortAsc;
            else {
                sortField = field;
                sortAsc = true;
            }

            // remove ícones de todos os headers
            document.querySelectorAll('#tabela thead th[data-sort]').forEach(header => {
                header.innerHTML = header.textContent.trim();
            });

            // adiciona ícone na coluna ativa
            const icon = sortAsc ? 'bi-caret-up-fill' : 'bi-caret-down-fill';
            th.innerHTML = `${th.textContent.trim()} <i class="bi ${icon} ms-1"></i>`;

            // recarrega a tabela já ordenada
            carregarAgendamentos();
        });
    });

    // ============ FLATPICKR ============

    const datePicker = flatpickr('#date-input', {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        allowInput: true
    });
    const startTimePicker = flatpickr('#start-time-input', {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        time_24hr: true
    });
    const endTimePicker = flatpickr('#end-time-input', {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'H:i',
        time_24hr: true
    });
    const flatpickrInstances = [datePicker, startTimePicker, endTimePicker];

    // ============ BOTÕES DE LIMPAR ============

    document.querySelectorAll('.clear-input').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;
            if (input._flatpickr) input._flatpickr.clear();
            else input.value = '';
            input.dispatchEvent(new Event('input'));
            input.dispatchEvent(new Event('change'));
        });
    });

    // ============ INICIAL ============

    carregarAgendamentos();

    function renderAccordion(agendamentos) {
        const container = document.getElementById('agendamentosAccordion');
        container.innerHTML = '';

        if (agendamentos.length === 0) {
            container.innerHTML = `<div class="text-center p-3">Nenhum agendamento encontrado.</div>`;
            return;
        }

        agendamentos.forEach((ag, index) => {
            const paciente = ag.paciente ? ag.paciente.NOME_COMPL_PACIENTE : '-';
            const aluno = ag.aluno ? ag.aluno.NOME_COMPL : (ag.ID_ALUNO || '-');
            const servico = ag.servico ? ag.servico.SERVICO_CLINICA_DESC : '-';
            const data = ag.DT_AGEND ? new Date(ag.DT_AGEND).toLocaleDateString('pt-BR', { timeZone: 'UTC' }) : '-';
            const horaIni = ag.HR_AGEND_INI ? ag.HR_AGEND_INI.substring(0, 5) : '-';
            const horaFim = ag.HR_AGEND_FIN ? ag.HR_AGEND_FIN.substring(0, 5) : '-';
            const local = ag.LOCAL ?? '-';
            const status = ag.STATUS_AGEND ?? '-';
            const valor = ag.VALOR_AGEND ? parseFloat(ag.VALOR_AGEND).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '-';
            const checkPagamento = ag.STATUS_PAG === 'S'
                ? '<span class="badge bg-success">Sim</span>'
                : '<span class="badge bg-danger">Não</span>';
            const valorPagamento = ag.VALOR_PAG ? parseFloat(ag.VALOR_PAG).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : '-';
            const reagendamento = ag.ID_AGEND_REMARCADO != null ? 'Sim' : 'Não';

            const accordionItem = document.createElement('div');
            accordionItem.className = 'accordion-item mb-2';

            accordionItem.innerHTML = `
                <h2 class="accordion-header" id="heading${index}">
                    <button class="accordion-button collapsed" type="button" aria-expanded="false">
                        ${paciente} - ${data} às ${horaIni}
                    </button>
                </h2>
                <div class="accordion-content">
                    <div class="accordion-body">
                        <ul class="list-unstyled mb-0">
                            <li><strong>Aluno:</strong> ${aluno}</li>
                            <li><strong>Serviço:</strong> ${servico}</li>
                            <li><strong>Data:</strong> ${data}</li>
                            <li><strong>Hora Início:</strong> ${horaIni}</li>
                            <li><strong>Hora Fim:</strong> ${horaFim}</li>
                            <li><strong>Local:</strong> ${local}</li>
                            <li><strong>Status:</strong> ${status}</li>
                            <li><strong>Reagendamento?</strong> ${reagendamento}</li>
                            <li><strong>Valor:</strong> ${valor}</li>
                            <li><strong>Pago?</strong> ${checkPagamento}</li>
                            <li><strong>Valor Pago:</strong> ${valorPagamento}</li>
                            <li class="mt-2">
                                <a href="/psicologia/agendamento/${ag.ID_AGENDAMENTO}/editar" class="btn btn-sm btn-warning">Editar</a>
                                <form action="/psicologia/agendamento/${ag.ID_AGENDAMENTO}" method="POST" class="d-inline" onsubmit="return confirm('Confirma a exclusão deste agendamento?');">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            `;

            const btn = accordionItem.querySelector('.accordion-button');
            const content = accordionItem.querySelector('.accordion-content');

            // inicial
            content.style.maxHeight = '0px';
            content.style.overflow = 'hidden';
            content.style.transition = 'max-height 0.3s ease';

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const isOpen = btn.getAttribute('aria-expanded') === 'true';

                if (isOpen) {
                    // fechar
                    content.style.maxHeight = '0px';
                    btn.classList.add('collapsed');
                    btn.setAttribute('aria-expanded', 'false');
                } else {
                    // abrir
                    // fecha outros se necessário (opcional)
                    container.querySelectorAll('.accordion-content').forEach(c => {
                        if (c !== content) {
                            c.style.maxHeight = '0px';
                            const otherBtn = c.parentElement.querySelector('.accordion-button');
                            if (otherBtn) {
                                otherBtn.classList.add('collapsed');
                                otherBtn.setAttribute('aria-expanded', 'false');
                            }
                        }
                    });

                    content.style.maxHeight = content.scrollHeight + 'px';
                    btn.classList.remove('collapsed');
                    btn.setAttribute('aria-expanded', 'true');
                }
            });

            // recalcula altura se window resize
            window.addEventListener('resize', () => {
                if (btn.getAttribute('aria-expanded') === 'true') {
                    content.style.maxHeight = content.scrollHeight + 'px';
                }
            });

            container.appendChild(accordionItem);
        });
    }

    function makeColumnsResizable(table) {
        // Garante que a tabela use layout fixo
        table.style.tableLayout = 'fixed';
        
        const ths = table.querySelectorAll('th');
        
        ths.forEach((th, index) => {
            // Cria handle de redimensionamento
            const grip = document.createElement('div');
            grip.style.position = 'absolute';
            grip.style.right = '0';
            grip.style.top = '0';
            grip.style.height = '100%';
            grip.style.width = '5px';
            grip.style.cursor = 'col-resize';
            grip.style.userSelect = 'none';
            grip.style.touchAction = 'none';
            grip.style.zIndex = '1';
            th.style.position = 'relative';
            th.appendChild(grip);

            let startX, startWidth;

            grip.addEventListener('mousedown', (e) => {
                e.preventDefault(); // evita seleção de texto
                startX = e.pageX;
                startWidth = th.offsetWidth;

                function onMouseMove(eMove) {
                    const newWidth = startWidth + (eMove.pageX - startX);
                    if (newWidth > 30) { // largura mínima
                        th.style.width = newWidth + 'px';

                        // aplica a mesma largura para cada td da coluna
                        table.querySelectorAll('tr').forEach(tr => {
                            const cell = tr.children[index];
                            if (cell) cell.style.width = newWidth + 'px';
                        });
                    }
                }

                function onMouseUp() {
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);
                }

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            });
        });
    }

    // Chamando a função
    const tabela = document.getElementById('tabela').querySelector('table');
    makeColumnsResizable(tabela);

});

document.addEventListener('DOMContentLoaded', function () {
    const navbar = document.getElementById('mainNavbar');
    const content = document.getElementById('content');

    // Função para atualizar largura do content
    function adjustContentWidth() {
        if (navbar.classList.contains('collapsed')) {
            content.style.marginLeft = '80px';
        } else {
            content.style.marginLeft = '250px';
        }
    }

    // Se você tiver um botão para colapsar a navbar
    const toggleBtn = document.getElementById('toggle-navbar'); // ID do botão que colapsa
    toggleBtn.addEventListener('click', () => {
        navbar.classList.toggle('collapsed');
        adjustContentWidth();
    });

    // Ajusta na primeira carga
    adjustContentWidth();
});
