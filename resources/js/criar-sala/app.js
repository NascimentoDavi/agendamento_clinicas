// public/js/salas.js

import 'bootstrap';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';

// === FUNÇÃO DE ALERTA MODAL ===
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

document.addEventListener('DOMContentLoaded', () => {
    // === ELEMENTOS DOM ===
    const salasTbody = document.getElementById('salas-tbody');
    const searchInput = document.getElementById('search-sala');
    const formEditarSala = document.getElementById('form-editar-sala');
    const editarSalaModalEl = document.getElementById('editarSalaModal');
    const btnDeletar = document.getElementById('btn-deletar-sala');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let editarSalaModal = null;
    if (editarSalaModalEl) {
        editarSalaModal = new bootstrap.Modal(editarSalaModalEl);
    }

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

    // === ATIVAR BOTÕES EDITAR NA TABELA ===
    function ativarEventosTabela() {
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (!formEditarSala) return;

                const sala = JSON.parse(btn.dataset.sala);

                formEditarSala.querySelector('#edit-sala-id').value = sala.ID_SALA_CLINICA;
                formEditarSala.querySelector('#edit-sala-desc').value = sala.DESCRICAO;
                formEditarSala.querySelector('#edit-sala-status').value = sala.ATIVO;

                const selectDisc = formEditarSala.querySelector('#edit-sala-disc');
                await carregarDisciplinas(selectDisc, sala.DISCIPLINA);

                if (editarSalaModal) editarSalaModal.show();
            });
        });
    }

    // === CARREGAR SALAS ===
    async function carregarSalas(search = '') {
        if (!salasTbody) return;

        salasTbody.innerHTML = `<tr><td colspan="4" class="text-center">Carregando...</td></tr>`;

        try {
            const response = await fetch(`/psicologia/salas/listar?search=${encodeURIComponent(search)}`);
            const salas = await response.json();

            if (salas.length === 0) {
                salasTbody.innerHTML = `<tr><td colspan="4" class="text-center">Nenhuma sala encontrada.</td></tr>`;
                return;
            }

            salasTbody.innerHTML = '';
            salas.forEach(s => {
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
            console.error("Erro ao carregar salas:", error);
            salasTbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Erro ao carregar salas.</td></tr>`;
        }
    }

    // === EVENTOS ===
    if (searchInput) {
        searchInput.addEventListener('input', () => carregarSalas(searchInput.value));
    }

    if (formEditarSala) {
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
                editarSalaModal.hide();
                showModalAlert(body.message || 'Sala atualizada com sucesso!', 'success');
                setTimeout(() => window.location.reload(), 2000);
            })
            .catch(err => showModalAlert(err.message));
        });
    }

    if (btnDeletar) {
        btnDeletar.addEventListener('click', () => {
            if (!confirm('Tem certeza que deseja excluir esta sala? Esta ação não pode ser desfeita.')) return;

            const id = formEditarSala.querySelector('#edit-sala-id').value;

            fetch(`/psicologia/salas/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            })
            .then(res => res.json().then(body => ({ ok: res.ok, body })))
            .then(({ ok, body }) => {
                if (!ok) throw new Error(body.message || 'Erro ao excluir.');
                editarSalaModal.hide();
                showModalAlert(body.message || 'Sala excluída com sucesso!', 'success');
                setTimeout(() => window.location.reload(), 2000);
            })
            .catch(err => showModalAlert(err.message));
        });
    }

    // === INICIALIZAÇÃO ===
    carregarSalas();
    carregarDisciplinas(document.getElementById('disciplina-sala'));
});
