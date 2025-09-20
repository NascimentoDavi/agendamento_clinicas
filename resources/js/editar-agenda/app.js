// resources/js/consultar-agendamento/app.js

import flatpickr from "flatpickr";
import { Portuguese } from "flatpickr/dist/l10n/pt.js";
import TomSelect from "tom-select";

// Localiza o Flatpickr para português
flatpickr.localize(Portuguese);

document.addEventListener('DOMContentLoaded', function () {

    const btnEditar = document.getElementById('btn-editar');
    const btnSalvar = document.getElementById('btn-salvar');

    let editModeInicializado = false;

    if (btnEditar) {
        btnEditar.addEventListener('click', function () {

            // Habilita todos os campos editáveis
            document.querySelectorAll('.editable-field').forEach(el => el.removeAttribute('disabled'));

            // Mostra os selects / inputs editáveis e esconde os view-only
            document.querySelectorAll('.view-mode').forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('d-none'));

            // Troca os botões
            btnSalvar.classList.remove('d-none');
            btnEditar.classList.add('d-none');

            // Inicializa plugins apenas na primeira vez
            if (!editModeInicializado) {
                inicializarFlatpickr();
                inicializarTomSelects();
                editModeInicializado = true;
            }
        });
    }

    /**
     * Inicializa os campos de data e hora com Flatpickr
     */
    function inicializarFlatpickr() {
        const dateConfig = {
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            allowInput: true,
        };

        const timeConfig = {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minuteIncrement: 15,
            allowInput: true,
        };

        if (document.querySelector('#DT_AGEND')) {
            flatpickr("#DT_AGEND", dateConfig);
        }

        if (document.querySelector('#HR_AGEND_INI')) {
            flatpickr("#HR_AGEND_INI", timeConfig);
        }

        if (document.querySelector('#HR_AGEND_FIN')) {
            flatpickr("#HR_AGEND_FIN", timeConfig);
        }
    }

    /**
     * Inicializa os TomSelects para selects editáveis
     */
    function inicializarTomSelects() {

        // --- Paciente ---
        if (document.querySelector('#select-paciente')) {
            new TomSelect('#select-paciente', {
                valueField: 'ID_PACIENTE',
                labelField: 'NOME_COMPL_PACIENTE',
                searchField: ['NOME_COMPL_PACIENTE', 'CPF_PACIENTE'],
                create: false,
                load: (query, callback) => {
                    if (query.length < 2) return callback();
                    fetch(`/psicologia/consultar-paciente/buscar-nome-cpf?search=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(json => callback(json))
                        .catch(() => callback());
                },
                render: {
                    option: (data, escape) => `<div><strong>${escape(data.NOME_COMPL_PACIENTE)}</strong><small class="d-block text-muted">${escape(data.CPF_PACIENTE || '')}</small></div>`,
                    item: (data, escape) => `<div>${escape(data.NOME_COMPL_PACIENTE)}</div>`,
                    no_results: (data, escape) => {
                        const query = encodeURIComponent(data.input || '');
                        return `<div class="no-results">Nenhum paciente encontrado. <a href="/psicologia/criar-paciente?nome_compl_paciente=${query}" target="_blank" class="text-primary fw-bold">Criar novo paciente</a></div>`;
                    }
                }
            });
        }

        // --- Serviço ---
        if (document.querySelector('#select-servico')) {
            new TomSelect('#select-servico', {
                valueField: 'ID_SERVICO_CLINICA',
                labelField: 'SERVICO_CLINICA_DESC',
                searchField: ['SERVICO_CLINICA_DESC'],
                create: false,
                load: (query, callback) => {
                    const url = `/psicologia/pesquisar-servico?search=${encodeURIComponent(query)}`;
                    fetch(url).then(r => r.json()).then(j => callback(j)).catch(() => callback());
                },
                render: {
                    no_results: (data, escape) => {
                        const query = encodeURIComponent(data.input || '');
                        return `<div class="no-results">Nenhum serviço encontrado. <a href="/psicologia/criar-servico?SERVICO_CLINICA_DESC=${query}" target="_blank" class="text-primary fw-bold">Criar novo serviço</a></div>`;
                    }
                }
            });
        }

        // --- Local ---
        const servicoSelectEl = document.querySelector('#select-servico');
        if (document.querySelector('#select-local')) {
            const localSelectInstance = new TomSelect('#select-local', {
                valueField: 'ID_SALA_CLINICA',
                labelField: 'DESCRICAO',
                searchField: ['DESCRICAO'],
                create: false,
                load: (query, callback) => {
                    const servicoId = servicoSelectEl ? servicoSelectEl.value : null;
                    if (!query.length && !servicoId) return callback();
                    const url = `/psicologia/pesquisar-local?search=${encodeURIComponent(query)}&servico=${encodeURIComponent(servicoId || '')}`;
                    fetch(url)
                        .then(r => r.json())
                        .then(json => callback(json))
                        .catch(() => callback());
                },
                render: {
                    no_results: (data, escape) => {
                        const query = encodeURIComponent(data.input || '');
                        return `<div class="no-results">Nenhum local encontrado. 
                                <a href="/psicologia/criar-sala?DESCRICAO=${query}" target="_blank" class="text-primary fw-bold">
                                Criar nova sala</a></div>`;
                    }
                }
            });

            // Atualiza o select de local quando o serviço mudar
            if (servicoSelectEl) {
                servicoSelectEl.addEventListener('change', () => {
                    localSelectInstance.clear();
                    localSelectInstance.load(""); // recarrega filtrando pelo serviço selecionado
                });
            }
        }
    }

    const servicoSelectEl = document.querySelector('#select-servico');

if (document.querySelector('#select-aluno')) {
    const alunoSelectInstance = new TomSelect('#select-aluno', {
        valueField: 'ID_ALUNO',
        labelField: 'NOME_COMPL',
        searchField: ['ID_ALUNO', 'NOME_COMPL'],
        create: false,
        load: async (query, callback) => {
            if (!query.length) return callback();

            let disciplina = '';

            if (servicoSelectEl && servicoSelectEl.value) {
                // Busca a disciplina do serviço selecionado
                try {
                    const res = await fetch(`/psicologia/pesquisar-disciplina-servico?id=${encodeURIComponent(servicoSelectEl.value)}`);
                    const data = await res.json();
                    disciplina = data.disciplina || '';
                } catch (err) {
                    console.error('Erro ao buscar disciplina:', err);
                    disciplina = '';
                }
            }

            const url = `/psicologia/listar-alunos?search=${encodeURIComponent(query)}&disciplina=${encodeURIComponent(disciplina)}`;
            fetch(url)
                .then(r => r.json())
                .then(j => callback(j))
                .catch(() => callback());
        },
        render: {
            option: (data, escape) => `<div>${escape(data.NOME_COMPL)} - ${escape(data.ID_ALUNO)}</div>`,
            item: (data, escape) => `<div>${escape(data.NOME_COMPL)}</div>`
        },
        onChange: (value) => {
            const hiddenInput = document.getElementById('id_aluno_input');
            if (hiddenInput) hiddenInput.value = value || '';
        }
    });

    // Recarrega o select de aluno quando o serviço mudar
    if (servicoSelectEl) {
        servicoSelectEl.addEventListener('change', () => {
            alunoSelectInstance.clear();
            alunoSelectInstance.load(""); // recarrega com a nova disciplina
        });
    }
}

    // --- Formatação de valor ---
    const valorInput = document.getElementById('valor_edit_agenda');
    if (valorInput) {
        valorInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (value / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
            e.target.value = value;
        });
    }
});
