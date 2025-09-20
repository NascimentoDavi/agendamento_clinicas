import 'bootstrap';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import { Portuguese } from 'flatpickr/dist/l10n/pt.js';

// Localização PT-BR
flatpickr.localize(Portuguese);

// --- Campo do SUS ---
const codSusCheck = document.getElementById('cod_sus_check');
if (codSusCheck) {
    codSusCheck.addEventListener('input', function () {
        const codSusDiv = document.getElementById('cod-sus-div');
        if (this.checked) {
            codSusDiv.innerHTML = `
                <label for="cod-sus-input" class="form-label">Cód. SUS <small class="text-muted">(CNS)</small></label>
                <input type="text" class="form-control" name="COD_SUS" id="cod-sus-input" placeholder="0000-0000-0000-0000">`;
            
            document.getElementById('cod-sus-input').addEventListener('input', function () {
                let value = this.value.replace(/\D/g, '').slice(0, 15);
                this.value = value.match(/.{1,4}/g)?.join('-') || '';
            });
        } else {
            codSusDiv.innerHTML = '';
        }
    });
}

// --- API ViaCEP ---
const cepField = document.getElementById('cep');
if (cepField) {
    cepField.addEventListener('blur', function() {
        let cep = this.value.replace(/\D/g, '');
        if (cep.length !== 8) return;

        const fields = {
            rua: document.getElementById('rua'),
            bairro: document.getElementById('bairro'),
            MUNICIPIO: document.getElementById('MUNICIPIO'),
            estado: document.getElementById('estado'),
            numero: document.getElementById('numero')
        };

        Object.values(fields).forEach(f => f.disabled = true);
        fields.rua.value = 'Buscando...';

        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    alert('CEP não encontrado.');
                    Object.values(fields).forEach(f => f.value = '');
                } else {
                    fields.rua.value = data.logradouro || '';
                    fields.bairro.value = data.bairro || '';
                    fields.MUNICIPIO.value = data.localidade || '';
                    fields.estado.value = data.uf || '';
                    fields.numero.focus();
                }
            })
            .catch(() => alert('Erro ao consultar o CEP.'))
            .finally(() => Object.values(fields).forEach(f => f.disabled = false));
    });
}

// --- Flatpickr ---
flatpickr("#dt_nasc", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d/m/Y",
    maxDate: "today",
    locale: "pt",
    allowInput: true,
    defaultDate: "{{ old('DT_NASC_PACIENTE') ?? '' }}",
    onChange: validarResponsavel
});

// --- Alertas automáticos ---
document.addEventListener("DOMContentLoaded", function () {
    const alerts = document.querySelectorAll(".animate-alert");
    alerts.forEach(alert => {
        setTimeout(() => alert.remove(), 5000);
    });
});

// --- Bloqueio do Enter ---
const pacienteForm = document.getElementById('pacienteForm');
if (pacienteForm) {
    pacienteForm.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && event.target.tagName.toLowerCase() !== 'textarea') {
            event.preventDefault();
        }
    });
}

// --- Máscaras ---
function applyMask(elementId, maskFunction) {
    const el = document.getElementById(elementId);
    if (el) el.addEventListener('input', maskFunction);
}

const cpfMask = (e) => {
    let value = e.target.value.replace(/\D/g, '').slice(0, 11);
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    e.target.value = value;
};

const phoneMask = (e) => {
    let value = e.target.value.replace(/\D/g, '').slice(0, 11);
    if (value.length > 2) value = `(${value.substring(0,2)}) ${value.substring(2)}`;
    if (value.length > 9) value = value.replace(/(\d{5})(\d{4})/, '$1-$2');
    else if (value.length > 5) value = value.replace(/(\d{4})(\d{4})/, '$1-$2');
    e.target.value = value;
};

applyMask('cpf_paciente', cpfMask);
applyMask('cpf_responsavel', cpfMask);
applyMask('telefone', phoneMask);

// --- Responsável ---
function calcularIdade(dataNascimento) {
    const hoje = new Date();
    const nascimento = new Date(dataNascimento);
    let idade = hoje.getFullYear() - nascimento.getFullYear();
    const mes = hoje.getMonth() - nascimento.getMonth();
    if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) idade--;
    return idade;
}

function validarResponsavel() {
    const dtNasc = document.getElementById('dt_nasc').value;
    const nomeResp = document.getElementById('nome_responsavel');
    const cpfResp = document.getElementById('cpf_responsavel');

    if (!dtNasc) return;

    const idade = calcularIdade(dtNasc);

    if (idade < 18) {
        nomeResp.setAttribute('required', 'true');
        cpfResp.setAttribute('required', 'true');
        nomeResp.previousElementSibling.innerHTML = 'Nome Completo <span class="required-field">*</span>';
        cpfResp.previousElementSibling.innerHTML = 'CPF do Responsável <span class="required-field">*</span>';
    } else {
        nomeResp.removeAttribute('required');
        cpfResp.removeAttribute('required');
        nomeResp.previousElementSibling.innerHTML = 'Nome Completo';
        cpfResp.previousElementSibling.innerHTML = 'CPF do Responsável';
    }
}

window.addEventListener('DOMContentLoaded', validarResponsavel);
