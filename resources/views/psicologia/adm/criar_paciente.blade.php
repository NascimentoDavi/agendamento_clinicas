<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cadastro de Paciente</title>
    
    <link rel="icon" type="image/png" href="/favicon_faesa.png">

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />

    <style>
        .shadow-dark {
            box-shadow: 0 0.75rem 1.25rem rgba(0,0,0,0.4) !important;
        }
        /* Animação para alertas */
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
        .required-field {
            color: #dc3545; /* Cor de perigo do Bootstrap */
        }
    </style>
</head>

<body class="bg-body-secondary">
    @include('components.navbar')

    @if ($errors->any())
        <div class="alert alert-danger shadow text-center position-fixed top-0 start-50 translate-middle-x mt-3 animate-alert" style="max-width: 90%;">
            <strong>Ops!</strong> Corrija os itens abaixo:
            <ul class="mb-0 mt-1 list-unstyled">
                @foreach ($errors->all() as $error)
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

    @if (session('error'))
        <div class="alert alert-danger text-center shadow position-fixed top-0 start-50 translate-middle-x mt-3 animate-alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="container ms-3 me-3 mw-100">
        
        <div class="row">
            

             <x-page-title>
            </x-page-title>
            
            <div class="col-12 shadow-lg shadow-dark p-4 bg-body-tertiary rounded">

                <form action="{{ route('criarPaciente-Psicologia') }}" method="POST" class="needs-validation" id="pacienteForm">
                    @csrf

                    <h5><i class="bi bi-person-vcard me-2"></i>Dados Pessoais</h5>
                    <hr class="mb-4"/>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="nome" class="form-label">Nome Completo <span class="required-field">*</span></label>
                            <input type="text" id="nome" name="NOME_COMPL_PACIENTE" class="form-control" value="{{ old('NOME_COMPL_PACIENTE', request('nome_compl_paciente')) }}" required />
                        </div>
                        <div class="col-md-3">
                            <label for="dt_nasc" class="form-label">Data de Nascimento</label>
                            <input type="text" id="dt_nasc" name="DT_NASC_PACIENTE" class="form-control" placeholder="dd/mm/aaaa" value="{{ old('DT_NASC_PACIENTE') }}" required/>
                        </div>
                        <div class="col-md-3">
                            <label for="cpf_paciente" class="form-label">CPF <span class="required-field">*</span></label>
                            <input type="text" id="cpf_paciente" name="CPF_PACIENTE" class="form-control" value="{{ old('CPF_PACIENTE') }}" required />
                        </div>
                        <div class="col-md-4">
                            <label for="sexo" class="form-label">Sexo <span class="required-field">*</span></label>
                            <select id="sexo" name="SEXO_PACIENTE" class="form-select" required>
                                <option value="" selected disabled>Selecione...</option>
                                <option value="M" @if(old('SEXO_PACIENTE') == 'M') selected @endif>Masculino</option>
                                <option value="F" @if(old('SEXO_PACIENTE') == 'F') selected @endif>Feminino</option>
                                <option value="O" @if(old('SEXO_PACIENTE') == 'O') selected @endif>Outro</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                             <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="cod_sus_check" id="cod_sus_check">
                                <label class="form-check-label" for="cod_sus_check">Possui cartão do SUS</label>
                             </div>
                        </div>
                        <div class="col-md-4" id="cod-sus-div"></div>
                    </div>

                    <h5><i class="bi bi-person-badge me-2"></i>Dados do Responsável <small class="text-muted">(Opcional)</small></h5>
                    <hr class="mb-4"/>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label for="nome_responsavel" class="form-label">Nome Completo</label>
                            <input type="text" id="nome_responsavel" name="NOME_RESPONSAVEL" class="form-control" value="{{ old('NOME_RESPONSAVEL') }}"/>
                        </div>
                        <div class="col-md-4">
                            <label for="cpf_responsavel" class="form-label">CPF do Responsável</label>
                            <input type="text" id="cpf_responsavel" name="CPF_RESPONSAVEL" class="form-control" value="{{ old('CPF_RESPONSAVEL') }}"/>
                        </div>
                    </div>
                    
                    <h5><i class="bi bi-geo-alt me-2"></i>Endereço</h5>
                    <hr class="mb-4"/>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="cep" class="form-label">CEP <span class="required-field">*</span></label>
                            <input type="text" id="cep" name="CEP" class="form-control" value="{{ old('CEP') }}" required />
                        </div>
                        <div class="col-md-7">
                            <label for="rua" class="form-label">Rua <span class="required-field">*</span></label>
                            <input type="text" id="rua" name="ENDERECO" class="form-control" value="{{ old('ENDERECO') }}" required />
                        </div>
                        <div class="col-md-2">
                            <label for="numero" class="form-label">Número <span class="required-field">*</span></label>
                            <input type="text" id="numero" name="END_NUM" class="form-control" value="{{ old('END_NUM') }}" required />
                        </div>
                        <div class="col-md-4">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" id="complemento" name="COMPLEMENTO" class="form-control" value="{{ old('COMPLEMENTO') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="bairro" class="form-label">Bairro <span class="required-field">*</span></label>
                            <input type="text" id="bairro" name="BAIRRO" class="form-control" value="{{ old('BAIRRO') }}" required />
                        </div>
                        <div class="col-md-4">
                            <label for="MUNICIPIO" class="form-label">Município <span class="required-field">*</span></label>
                            <input type="text" id="MUNICIPIO" name="MUNICIPIO" class="form-control" value="{{ old('MUNICIPIO') }}" required />
                        </div>
                        <div class="col-md-4">
                            <label for="estado" class="form-label">Estado <span class="required-field">*</span></label>
                            <select id="estado" name="UF" class="form-select" required>
                                <option value="" selected disabled>Selecione...</option>
                                <option value="AC" @if(old('UF') == 'AC') selected @endif>Acre</option>
                                <option value="AL" @if(old('UF') == 'AL') selected @endif>Alagoas</option>
                                <option value="AP" @if(old('UF') == 'AP') selected @endif>Amapá</option>
                                <option value="AM" @if(old('UF') == 'AM') selected @endif>Amazonas</option>
                                <option value="BA" @if(old('UF') == 'BA') selected @endif>Bahia</option>
                                <option value="CE" @if(old('UF') == 'CE') selected @endif>Ceará</option>
                                <option value="DF" @if(old('UF') == 'DF') selected @endif>Distrito Federal</option>
                                <option value="ES" @if(old('UF') == 'ES') selected @endif>Espírito Santo</option>
                                <option value="GO" @if(old('UF') == 'GO') selected @endif>Goiás</option>
                                <option value="MA" @if(old('UF') == 'MA') selected @endif>Maranhão</option>
                                <option value="MT" @if(old('UF') == 'MT') selected @endif>Mato Grosso</option>
                                <option value="MS" @if(old('UF') == 'MS') selected @endif>Mato Grosso do Sul</option>
                                <option value="MG" @if(old('UF') == 'MG') selected @endif>Minas Gerais</option>
                                <option value="PA" @if(old('UF') == 'PA') selected @endif>Pará</option>
                                <option value="PB" @if(old('UF') == 'PB') selected @endif>Paraíba</option>
                                <option value="PR" @if(old('UF') == 'PR') selected @endif>Paraná</option>
                                <option value="PE" @if(old('UF') == 'PE') selected @endif>Pernambuco</option>
                                <option value="PI" @if(old('UF') == 'PI') selected @endif>Piauí</option>
                                <option value="RJ" @if(old('UF') == 'RJ') selected @endif>Rio de Janeiro</option>
                                <option value="RN" @if(old('UF') == 'RN') selected @endif>Rio Grande do Norte</option>
                                <option value="RS" @if(old('UF') == 'RS') selected @endif>Rio Grande do Sul</option>
                                <option value="RO" @if(old('UF') == 'RO') selected @endif>Rondônia</option>
                                <option value="RR" @if(old('UF') == 'RR') selected @endif>Roraima</option>
                                <option value="SC" @if(old('UF') == 'SC') selected @endif>Santa Catarina</option>
                                <option value="SP" @if(old('UF') == 'SP') selected @endif>São Paulo</option>
                                <option value="SE" @if(old('UF') == 'SE') selected @endif>Sergipe</option>
                                <option value="TO" @if(old('UF') == 'TO') selected @endif>Tocantins</option>
                            </select>
                        </div>
                    </div>

                    <h5><i class="bi bi-telephone me-2"></i>Contato</h5>
                    <hr class="mb-4"/>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="E_MAIL_PACIENTE" class="form-control" value="{{ old('E_MAIL_PACIENTE') }}"/>
                        </div>
                        <div class="col-md-6">
                            <label for="telefone" class="form-label">Telefone <span class="required-field">*</span></label>
                            <input type="tel" id="telefone" name="FONE_PACIENTE" class="form-control" value="{{ old('FONE_PACIENTE') }}" required />
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="observacao" class="form-label">Observações</label>
                        <textarea id="observacao" name="OBSERVACAO" class="form-control" rows="4">{{ old('OBSERVACAO') }}</textarea>
                    </div>

                    <input type="hidden" name="STATUS" value="Em espera">

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-2"></i>Salvar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>
    
    <script>
        // Lógica para o campo do SUS
        document.getElementById('cod_sus_check').addEventListener('input', function () {
            const codSusDiv = document.getElementById('cod-sus-div');
            if (this.checked) {
                codSusDiv.innerHTML = `
                    <label for="cod-sus-input" class="form-label">Cód. SUS <small class="text-muted">(CNS)</small></label>
                    <input type="text" class="form-control" name="COD_SUS" id="cod-sus-input" placeholder="000-0000-0000-0000">`;
                
                document.getElementById('cod-sus-input').addEventListener('input', function () {
                    let value = this.value.replace(/\D/g, '').slice(0, 15);
                    this.value = value.match(/.{1,4}/g)?.join('-') || '';
                });
            } else {
                codSusDiv.innerHTML = '';
            }
        });

        // API ViaCEP para preenchimento de endereço
        document.getElementById('cep').addEventListener('blur', function() {
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

        // Flatpickr para data de nascimento
        flatpickr.localize(flatpickr.l10ns.pt);
        flatpickr("#dt_nasc", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            maxDate: "today",
            locale: "pt",
            allowInput: true,
            defaultDate: "{{ old('DT_NASC_PACIENTE') ?? '' }}"
        });

        // Remoção de Alertas
        document.addEventListener("DOMContentLoaded", function () {
            const alerts = document.querySelectorAll(".animate-alert");
            alerts.forEach(alert => {
                setTimeout(() => alert.remove(), 5000);
            });
        });

        // Bloqueio do Enter no formulário
        document.getElementById('pacienteForm').addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && event.target.tagName.toLowerCase() !== 'textarea') {
                event.preventDefault();
            }
        });
        
        // Máscaras de CPF e Telefone
        function applyMask(elementId, maskFunction) {
            document.getElementById(elementId).addEventListener('input', maskFunction);
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
            if (value.length > 2) {
                value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
            }
            if (value.length > 9) { // Ajuste para celulares com 9 dígitos
                 value = value.replace(/(\d{5})(\d{4})/, '$1-$2');
            } else if (value.length > 5) { // Ajuste para telefones fixos
                 value = value.replace(/(\d{4})(\d{4})/, '$1-$2');
            }
            e.target.value = value;
        };
        
        applyMask('cpf_paciente', cpfMask);
        applyMask('cpf_responsavel', cpfMask);
        applyMask('telefone', phoneMask);
    </script>

    <!-- SCRIPT PARA OBRIGATORIEDADE DE PREENCHIMENTO DOS CAMPOS DE RESPONSÁVEL -->
     <script>
        // Função para calcular idade
        function calcularIdade(dataNascimento) {
            const hoje = new Date();
            const nascimento = new Date(dataNascimento);
            let idade = hoje.getFullYear() - nascimento.getFullYear();
            const mes = hoje.getMonth() - nascimento.getMonth();

            if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
                idade--;
            }
            return idade;
        }

        // Função para validar responsável
        function validarResponsavel() {
            const dtNasc = document.getElementById('dt_nasc').value;
            const nomeResp = document.getElementById('nome_responsavel');
            const cpfResp = document.getElementById('cpf_responsavel');

            if (dtNasc) {
                const idade = calcularIdade(dtNasc);

                if (idade < 18) {
                    nomeResp.setAttribute('required', 'true');
                    cpfResp.setAttribute('required', 'true');

                    nomeResp.previousElementSibling.innerHTML = 'Nome Completo do Responsável <span class="required-field">*</span>';
                    cpfResp.previousElementSibling.innerHTML = 'CPF do Responsável <span class="required-field">*</span>';
                } else {
                    nomeResp.removeAttribute('required');
                    cpfResp.removeAttribute('required');

                    nomeResp.previousElementSibling.innerHTML = 'Nome Completo';
                    cpfResp.previousElementSibling.innerHTML = 'CPF do Responsável';
                }
            }
        }

        // Ativar validação sempre que a data de nascimento mudar
        document.getElementById('dt_nasc').addEventListener('change', validarResponsavel);

        // Rodar validação inicial (ex: quando carrega página com old input)
        window.addEventListener('DOMContentLoaded', validarResponsavel);
    </script>
</body>
</html>