// public/js/servicos.js

import 'bootstrap';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';

// === FUNÇÕES GERAIS ===
function showModalAlert(message, type = 'danger') {
    const container = document.getElementById('modal-alert-container');
    if (!container) return;

    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show m-3`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    container.innerHTML = '';
    container.appendChild(alert);

    setTimeout(() => alert.classList.remove('show'), 4000);
}

function formatarValor(valor) {
    const valorNumerico = parseFloat(valor || 0);
    return valorNumerico.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function formatarInputValor(inputElement) {
    if (!inputElement) return;
    inputElement.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        value = (parseInt(value) / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
        if (value === 'NaN') value = '';
        e.target.value = value;
    });
}

// === SCRIPT PRINCIPAL ===
document.addEventListener('DOMContentLoaded', () => {
    const servicosTbody = document.getElementById('servicos-tbody');
    const searchInput = document.getElementById('search-servico');
    const formEditarServico = document.getElementById('form-editar-servico');
    const editarServicoModalEl = document.getElementById('editarServicoModal');
const modalElement = document.getElementById('editarServicoModal');
let editarServicoModal = null;

if (modalElement) {
    editarServicoModal = new bootstrap.Modal(modalElement);
}
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let disciplinasCache = null;
    let tomSelectInstances = {};

    // === CARREGAR DISCIPLINAS COM TOM SELECT ===
    async function carregarDisciplinas(selectElement, valorSelecionado = null) {
        if (!selectElement) return;
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

        let tomSelect = tomSelectInstances[selectId];
        if (tomSelect) {
            tomSelect.clear();
            tomSelect.clearOptions();
        }

        const options = disciplinasCache.map(d => ({
            value: d.DISCIPLINA,
            text: `${d.DISCIPLINA} - ${d.NOME}`
        }));

        if (!tomSelect) {
            tomSelect = new TomSelect(selectElement, {
                options: options,
                placeholder: 'Selecione ou pesquise...',
                create: false,
                sortField: { field: 'text', direction: 'asc' }
            });
            tomSelectInstances[selectId] = tomSelect;
        } else {
            tomSelect.addOptions(options);
        }

        if (valorSelecionado) {
            tomSelect.setValue(valorSelecionado);
        }
    }

    // === CARREGAR SERVIÇOS NA TABELA ===
    function carregarServicos(search = '') {
        if (!servicosTbody) return;

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
                            <button class="btn btn-sm btn-warning btn-editar" title="Editar"
                                data-servico='${JSON.stringify(s)}'>
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

    // === ATIVAR EVENTOS DA TABELA ===
    function ativarEventosTabela() {
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', async () => {
                const servico = JSON.parse(btn.dataset.servico);

                formEditarServico.querySelector('#edit-servico-id').value = servico.ID_SERVICO_CLINICA;
                formEditarServico.querySelector('#edit-servico-desc').value = servico.SERVICO_CLINICA_DESC;
                formEditarServico.querySelector('#edit-valor-servico').value = (servico.VALOR_SERVICO || '').toString().replace('.', ',');
                formEditarServico.querySelector('#edit-tempo-recorrencia-meses').value = servico.TEMPO_RECORRENCIA_MESES || '';
                formEditarServico.querySelector('#edit-observacao-servico').value = servico.OBSERVACAO || '';

                const selectDisc = formEditarServico.querySelector('#edit-servico-disc');
                await carregarDisciplinas(selectDisc, servico.DISCIPLINA);

                editarServicoModal.show();
            });
        });
    }

    // === SUBMIT EDITAR SERVIÇO ===
    if (formEditarServico) {
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
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
                .then(res => res.json().then(body => ({ ok: res.ok, body })))
                .then(({ ok, body }) => {
                    if (!ok) throw new Error(body.message || 'Erro ao salvar.');
                    editarServicoModal.hide();
                    showModalAlert(body.message || 'Serviço atualizado com sucesso!', 'success');
                    carregarServicos(searchInput?.value);
                })
                .catch(err => showModalAlert(err.message));
        });
    }

    // === BOTÃO DELETAR SERVIÇO ===
    const btnDeletar = document.getElementById('btn-deletar-servico');
    if (btnDeletar) {
        btnDeletar.addEventListener('click', () => {
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
                    showModalAlert(body.message || 'Serviço excluído com sucesso!', 'success');
                    carregarServicos(searchInput?.value);
                })
                .catch(err => showModalAlert(err.message));
        });
    }

    // === EVENTO PESQUISA ===
    if (searchInput) {
        searchInput.addEventListener('input', () => carregarServicos(searchInput.value));
    }

    // === FORMATAÇÃO AUTOMÁTICA DE CAMPOS DE VALOR ===
    formatarInputValor(document.getElementById('valor-servico'));
    formatarInputValor(document.getElementById('edit-valor-servico'));

    // === INICIALIZAÇÃO ===
    carregarServicos();
    carregarDisciplinas(document.getElementById('disciplina-servico'));
});
