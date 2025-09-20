
import flatpickr from "flatpickr";
import "flatpickr/dist/l10n/pt.js";

window.addEventListener('DOMContentLoaded', () => {

    flatpickr.localize(flatpickr.l10ns.pt);

    const horariosTbody = document.getElementById('horarios-tbody');
    const searchInput = document.getElementById('search-horario');
    const formEditarHorario = document.getElementById('form-editar-horario');
    const editarHorarioModalEl = document.getElementById('editarHorarioModal');
    const editarHorarioModal = new bootstrap.Modal(editarHorarioModalEl);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // --- Flatpickr para formulário de criação ---
    const configData = { dateFormat: "Y-m-d", altInput: true, altFormat: "d/m/Y", locale: "pt" };
    const configHora = { enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true };

    flatpickr("#DATA_HORARIO_INICIAL", configData);
    flatpickr("#DATA_HORARIO_FINAL", configData);
    flatpickr("#HR_HORARIO_INICIAL", configHora);
    flatpickr("#HR_HORARIO_FINAL", configHora);

    // --- Flatpickr para edição ---
    const flatpickrInstanceEditDataIni = flatpickr("#edit-data-horario-inicial", configData);
    const flatpickrInstanceEditDataFin = flatpickr("#edit-data-horario-final", configData);
    const flatpickrInstanceEditHoraIni = flatpickr("#edit-hr-horario-inicial", configHora);
    const flatpickrInstanceEditHoraFin = flatpickr("#edit-hr-horario-final", configHora);

    // --- Função de alerta modal ---
    function showModalAlert(message, type = 'danger') {
        const container = document.getElementById('modal-alert-container');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show m-3`;
        alert.innerHTML = `${message} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        container.innerHTML = '';
        container.appendChild(alert);
        setTimeout(() => alert.classList.remove('show'), 4000);
    }

    // --- Eventos dos botões de edição ---
    function ativarEventosTabela() {
        document.querySelectorAll('.btn-editar').forEach(btn => {
            btn.addEventListener('click', () => {
                const horario = JSON.parse(btn.dataset.horario);
                formEditarHorario.querySelector('#edit-horario-id').value = horario.ID_HORARIO;
                formEditarHorario.querySelector('#edit-horario-desc').value = horario.DESCRICAO_HORARIO;
                formEditarHorario.querySelector('#edit-tipo-horario').value = horario.BLOQUEADO;
                formEditarHorario.querySelector('#edit-observacao').value = horario.OBSERVACAO || '';

                flatpickrInstanceEditDataIni.setDate(horario.DATA_HORARIO_INICIAL, true);
                flatpickrInstanceEditDataFin.setDate(horario.DATA_HORARIO_FINAL, true);
                flatpickrInstanceEditHoraIni.setDate(horario.HR_HORARIO_INICIAL, true);
                flatpickrInstanceEditHoraFin.setDate(horario.HR_HORARIO_FINAL, true);

                editarHorarioModal.show();
            });
        });
    }

    // --- Carregar horários ---
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

    // --- Eventos ---
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

    // --- Inicialização ---
    carregarHorarios();
});