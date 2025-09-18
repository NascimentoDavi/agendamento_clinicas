// resources/js/criar-agenda/app.js

import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";
import { Portuguese } from "flatpickr/dist/l10n/pt.js";

import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

import "bootstrap/dist/js/bootstrap.bundle.min.js";

// ------------------- BLOQUEIO DE ENTER NO FORM -------------------
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("agendamento-form");
    if (form) {
        form.addEventListener("keydown", function (event) {
            if (event.key === "Enter" && event.target.tagName.toLowerCase() !== "textarea") {
                event.preventDefault();
            }
        });
    }
});

// ------------------- FUNÇÃO GENÉRICA PARA INICIALIZAR SELECT -------------------
function initializeTomSelectWithOldValue(selector, config) {
    const element = document.querySelector(selector);
    if (!element) return;

    const oldId = element.dataset.oldId;
    const ts = new TomSelect(element, config);

    if (oldId) ts.load(oldId);

    return ts;
}

// ------------------- SELECT DE PACIENTE -------------------
const pacienteSelect = initializeTomSelectWithOldValue("#select-paciente", {
    valueField: "ID_PACIENTE",
    labelField: "NOME_COMPL_PACIENTE",
    searchField: ["NOME_COMPL_PACIENTE", "CPF_PACIENTE"],

    load: (query, callback) => {
        if (!query.length) return callback();
        const url = `/psicologia/consultar-paciente/buscar-nome-cpf?search=${encodeURIComponent(query)}`;
        fetch(url).then(r => r.json()).then(json => callback(json)).catch(() => callback());
    },

    render: {
        no_results: function (data) {
            const query = encodeURIComponent(data.input || "");
            return `<div class="no-results">
                        Nenhum paciente encontrado. 
                        <a href="/psicologia/criar-paciente?nome_compl_paciente=${query}" 
                        target="_blank" class="text-primary fw-bold">
                        Criar novo paciente
                        </a>
                    </div>`;
        }
    }
});

// ------------------- SELECT DE SERVIÇO -------------------
const servicoSelect = initializeTomSelectWithOldValue("#select-servico", {
    valueField: "ID_SERVICO_CLINICA",
    labelField: "SERVICO_CLINICA_DESC",
    searchField: ["SERVICO_CLINICA_DESC"],

    load: (query, callback) => {
        if (!query.length) return callback();
        const url = `/psicologia/pesquisar-servico?search=${encodeURIComponent(query)}`;
        fetch(url).then(r => r.json()).then(json => callback(json)).catch(() => callback());
    },

    render: {
        no_results: function (data) {
            const query = encodeURIComponent(data.input || "");
            return `<div class="no-results">
                        Nenhum serviço encontrado. 
                        <a href="/psicologia/criar-servico?SERVICO_CLINICA_DESC=${query}" 
                        target="_blank" class="text-primary fw-bold">
                        Criar novo serviço
                        </a>
                    </div>`;
        }
    }
});

// ------------------- SELECT DE LOCAL -------------------
const localSelect = initializeTomSelectWithOldValue("#select-local", {
    valueField: "ID_SALA_CLINICA",
    labelField: "DESCRICAO",
    searchField: ["DESCRICAO"],

    load: (query, callback) => {
        const servicoId = document.querySelector("#select-servico").value;
        if (!query.length && !servicoId) return callback();

        const url = `/psicologia/pesquisar-local?search=${encodeURIComponent(query)}&servico=${encodeURIComponent(servicoId)}`;
        fetch(url).then(r => r.json()).then(json => callback(json)).catch(() => callback());
    },

    render: {
        no_results: function (data) {
            const query = encodeURIComponent(data.input || "");
            return `<div class="no-results">
                        Nenhum local encontrado. 
                        <a href="/psicologia/criar-sala?DESCRICAO=${query}" 
                        target="_blank" class="text-primary fw-bold">
                        Criar nova sala
                        </a>
                    </div>`;
        }
    }
});

// ------------------- SELECT DE ALUNO -------------------
const alunoSelect = initializeTomSelectWithOldValue("#select-aluno", {
    valueField: "ID_ALUNO",
    labelField: "NOME_COMPL",
    searchField: ["NOME_COMPL", "ID_ALUNO"],

    load: (query, callback) => {
        const servicoValue = servicoSelect?.getValue();
        let disciplina = "";
        if (servicoValue) {
            const selectedServico = servicoSelect.options[servicoValue];
            if (selectedServico && selectedServico.DISCIPLINA) {
                disciplina = selectedServico.DISCIPLINA;
            }
        }

        const url = `/psicologia/listar-alunos?search=${encodeURIComponent(query)}&disciplina=${encodeURIComponent(disciplina)}`;
        fetch(url).then(r => r.json()).then(json => callback(json)).catch(() => callback());
    }
});
if (alunoSelect) alunoSelect.disable();

// Atualiza quando o serviço muda
if (servicoSelect) {
    servicoSelect.on("change", (value) => {
        if (value) {
            const selectedItem = servicoSelect.options[value];
            if (selectedItem && selectedItem.VALOR_SERVICO) {
                const valor = parseFloat(selectedItem.VALOR_SERVICO).toLocaleString("pt-BR", { minimumFractionDigits: 2 });
                document.getElementById("valor_agend").value = valor;
            } else {
                document.getElementById("valor_agend").value = "";
            }

            alunoSelect.clear();
            alunoSelect.enable();
            alunoSelect.load("");
        } else {
            alunoSelect.clear();
            alunoSelect.disable();
            document.getElementById("valor_agend").value = "";
        }
    });
}

// ------------------- RECORRÊNCIA -------------------
document.addEventListener("DOMContentLoaded", function () {
    flatpickr.localize(Portuguese);

    const commonDateConfig = {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d/m/Y",
        locale: "pt",
        allowInput: true,
    };

    const commonTimeConfig = {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        minuteIncrement: 15,
        allowInput: true,
    };

    flatpickr("#data", { ...commonDateConfig, minDate: "today" });
    flatpickr("#hr_ini", commonTimeConfig);
    flatpickr("#hr_fim", commonTimeConfig);

    const fpDataFimInstance = flatpickr("#data_fim_recorrencia", { ...commonDateConfig, minDate: "today" });
    const inputDataFimVisivel = fpDataFimInstance.altInput;
    const inputDataFim = document.getElementById("data_fim_recorrencia");

    const tsDuracao = new TomSelect("#duracao_meses_recorrencia", {});

    function atualizarBloqueioCampos() {
        const duracaoPreenchida = tsDuracao.getValue() !== "";
        const dataFimPreenchida = inputDataFim.value !== "";

        inputDataFimVisivel.disabled = duracaoPreenchida;

        if (dataFimPreenchida) {
            tsDuracao.disable();
        } else {
            tsDuracao.enable();
        }
    }

    tsDuracao.on("change", atualizarBloqueioCampos);
    inputDataFim.addEventListener("input", atualizarBloqueioCampos);

    const temRecorrenciaCheckbox = document.getElementById("temRecorrencia");
    const recorrenciaCampos = document.getElementById("recorrenciaCampos");
    const msgRecorrencia = document.getElementById("msg-recorrencia");
    const recorrenciaInput = document.getElementById("recorrencia");
    const diasSemanaBtns = document.querySelectorAll("#diasSemanaBtns button");

    let container = document.getElementById("diasSemanaContainer");
    if (!container) {
        container = document.createElement("div");
        container.id = "diasSemanaContainer";
        container.style.display = "none";
        document.getElementById("agendamento-form").appendChild(container);
    }

    function atualizarDiasSelecionados() {
        container.innerHTML = "";
        const diasSelecionados = Array.from(diasSemanaBtns)
            .filter(btn => btn.classList.contains("active"))
            .map(btn => btn.getAttribute("data-dia"));
        diasSelecionados.forEach(dia => {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "dias_semana[]";
            input.value = dia;
            container.appendChild(input);
        });
    }

    diasSemanaBtns.forEach(btn => {
        btn.addEventListener("click", function () {
            this.classList.toggle("active");
            this.classList.toggle("btn-primary");
            this.classList.toggle("btn-outline-primary");
            atualizarDiasSelecionados();
        });
    });

    temRecorrenciaCheckbox.addEventListener("change", function () {
        if (this.checked) {
            recorrenciaCampos.classList.remove("d-none");
            msgRecorrencia.classList.remove("d-none");
            recorrenciaInput.value = crypto.randomUUID ? crypto.randomUUID() : "uuid-fallback-" + Date.now();
        } else {
            recorrenciaCampos.classList.add("d-none");
            msgRecorrencia.classList.add("d-none");
            recorrenciaInput.value = "";
            diasSemanaBtns.forEach(btn => {
                btn.classList.remove("active", "btn-primary");
                btn.classList.add("btn-outline-primary");
            });
            container.innerHTML = "";

            fpDataFimInstance.clear();
            tsDuracao.clear();
            atualizarBloqueioCampos();
        }
    });

    // ------------------- FORMATAÇÃO DO VALOR -------------------
    const valorInput = document.getElementById("valor_agend");
    if (valorInput) {
        valorInput.addEventListener("input", function (e) {
            let value = e.target.value.replace(/\D/g, "");
            value = (value / 100).toLocaleString("pt-BR", { minimumFractionDigits: 2 });
            e.target.value = value;
        });
    }
});
