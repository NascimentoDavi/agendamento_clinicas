<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pacientes por Aluno</title>

    <link rel="icon" type="image/png" href="/favicon_faesa.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
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

        /* Mantive seu estilo de botão não-collapsed */
        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0c63e4;
        }

        /* Estilos para o accordion custom (animação por max-height) */
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 280ms ease;
        }

        /* Garante que o body interno mantenha padding */
        .accordion-body {
            padding: .75rem 1rem;
        }
        .accordion-body .list-group-item {
            background-color: #e9ecef;
            padding-left: 1.5rem; 
        }

        .accordion-body .list-group-item:hover {
            background-color: #c3c7ca; 
        }

    </style>
</head>

<body class="bg-body-secondary">
    @include('components.professor_navbar')

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

    <div class="container ms-3 me-3 mw-100">
        <div class="row">
            <x-page-title></x-page-title>

            <div class="col-12 shadow-lg shadow-dark p-4 bg-body-tertiary rounded">
                <form id="search-form" class="w-100 mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-6 col-lg-4">
                            <label for="aluno-input" class="form-label fw-bold">Filtrar por aluno</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-workspace"></i></span>
                                <input id="aluno-input" name="aluno" type="search" class="form-control" placeholder="Nome ou Matrícula" />
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Pesquisar</button>
                            <button type="button" class="btn btn-outline-secondary" id="btnClearFilters">Limpar</button>
                        </div>
                    </div>
                </form>

                <hr>

                <h5 class="mb-3">Resultados Agrupados</h5>
                <div class="accordion" id="accordionalunos"></div>

            </div>
        </div>
    </div>

    <!-- Mantive o bundle do Bootstrap (se precisar remover em ambiente de teste para isolar conflitos, veja checklist abaixo) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchForm = document.getElementById('search-form');
            const accordionContainer = document.getElementById('accordionalunos');

            // Se true => permite múltiplos abertos; se false => um por vez (comportamento "accordion")
            const allowMultipleOpen = false;

            function getFilters() {
                const formData = new FormData(searchForm);
                return new URLSearchParams(formData);
            }

            function sanitizeId(value) {
                // Prefixa com 'aluno-' e troca caracteres não alfanuméricos por underscore
                return 'aluno-' + String(value).replace(/[^\w-]/g, '_');
            }

            function montarItem(aluno) {
                const safeId = sanitizeId(aluno.id);

                let pacientesHtml = '<ul class="list-group list-group-flush">';
                aluno.pacientes
                    .sort((a, b) => a.nome.localeCompare(b.nome, 'pt-BR'))
                    .forEach(paciente => {
                        pacientesHtml += `<li class="list-group-item"><strong>${escapeHtml(paciente.nome)}</strong> (CPF: ${escapeHtml(paciente.cpf)})</li>`;
                    });
                pacientesHtml += '</ul>';

                const accordionItem = document.createElement('div');
                accordionItem.className = 'accordion-item';

                accordionItem.innerHTML = `
                    <h2 class="accordion-header" id="heading-${safeId}">
                        <button class="accordion-button collapsed d-flex align-items-center" type="button"
                            aria-expanded="false"
                            aria-controls="${safeId}">
                            <strong class="me-3">${escapeHtml(aluno.nome)}</strong>
                            <span class="badge bg-primary rounded-pill ms-auto">${aluno.pacientes.length} Paciente(s)</span>
                        </button>
                    </h2>

                    <div id="${safeId}" class="accordion-content" aria-labelledby="heading-${safeId}">
                        <div class="accordion-body p-0">${pacientesHtml}</div>
                    </div>
                `;

                // referencias
                const btn = accordionItem.querySelector('.accordion-button');
                const content = accordionItem.querySelector('.accordion-content');

                // inicial state
                content.style.maxHeight = '0px';
                content.style.overflow = 'hidden';

                // função de abrir
                function openItem() {
                    if (!allowMultipleOpen) {
                        // fecha os outros
                        accordionContainer.querySelectorAll('.accordion-content').forEach(c => {
                            if (c !== content) {
                                c.style.maxHeight = '0px';
                                const otherBtn = c.parentElement.querySelector('.accordion-button');
                                if (otherBtn) {
                                    otherBtn.classList.add('collapsed');
                                    otherBtn.setAttribute('aria-expanded', 'false');
                                }
                            }
                        });
                    }

                    // expande o atual
                    // ajuste: primeiro remove maxHeight '0px' para obter scrollHeight correto
                    content.style.maxHeight = content.scrollHeight + 'px';
                    btn.classList.remove('collapsed');
                    btn.setAttribute('aria-expanded', 'true');
                }

                // função de fechar
                function closeItem() {
                    content.style.maxHeight = '0px';
                    btn.classList.add('collapsed');
                    btn.setAttribute('aria-expanded', 'false');
                }

                // toggle
                btn.addEventListener('click', (e) => {
                    e.preventDefault(); // evita qualquer ação indesejada
                    const isOpen = btn.getAttribute('aria-expanded') === 'true' || (content.style.maxHeight && content.style.maxHeight !== '0px');
                    if (isOpen) closeItem();
                    else openItem();
                });

                // Corrige altura em resize (se conteúdo mudar de tamanho)
                window.addEventListener('resize', () => {
                    if (btn.getAttribute('aria-expanded') === 'true') {
                        // recalcula para novo scrollHeight
                        content.style.maxHeight = content.scrollHeight + 'px';
                    }
                });

                // Após transição, se abriu, limpa maxHeight para permitir conteúdo fluido (opcional)
                content.addEventListener('transitionend', () => {
                    if (btn.getAttribute('aria-expanded') === 'true') {
                        // mantém um valor suficiente (não remove por segurança)
                        content.style.maxHeight = content.scrollHeight + 'px';
                    }
                });

                return accordionItem;
            }

            // Busca e agrupa (idêntico ao seu comportamento anterior)
            function buscarEAgruparAgendamentos() {
                const params = getFilters();
                const url = `/professor/consultar-agendamento/buscar?${params.toString()}`;

                accordionContainer.innerHTML = `
                    <div class="text-center p-4">
                        <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
                        <p class="mt-2">Carregando dados...</p>
                    </div>`;

                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Erro na resposta da rede.');
                        return response.json();
                    })
                    .then(agendamentos => {
                        const alunos = {};

                        agendamentos.forEach(ag => {
                            if (!ag.ID_ALUNO || !ag.paciente) return;

                            if (!alunos[ag.ID_ALUNO]) {
                                alunos[ag.ID_ALUNO] = {
                                    id: ag.ID_ALUNO,
                                    nome: 'aluno não identificado',
                                    pacientes: [],
                                    pacientes_ids: new Set()
                                };
                            }

                            if (ag.aluno && ag.aluno.NOME_COMPL) {
                                alunos[ag.ID_ALUNO].nome = ag.aluno.NOME_COMPL;
                            }

                            if (!alunos[ag.ID_ALUNO].pacientes_ids.has(ag.paciente.ID_PACIENTE)) {
                                alunos[ag.ID_ALUNO].pacientes.push({
                                    nome: ag.paciente.NOME_COMPL_PACIENTE,
                                    cpf: ag.paciente.CPF_PACIENTE || 'Não informado'
                                });
                                alunos[ag.ID_ALUNO].pacientes_ids.add(ag.paciente.ID_PACIENTE);
                            }
                        });

                        accordionContainer.innerHTML = '';
                        const alunosArray = Object.values(alunos);

                        if (alunosArray.length === 0) {
                            accordionContainer.innerHTML = `
                                <div class="text-center p-4">
                                    <p class="text-muted">Nenhum resultado encontrado para os filtros aplicados.</p>
                                </div>`;
                            return;
                        }

                        alunosArray.forEach(aluno => {
                            const item = montarItem(aluno);
                            accordionContainer.appendChild(item);
                        });
                    })
                    .catch(error => {
                        console.error('Erro ao buscar ou agrupar dados:', error);
                        accordionContainer.innerHTML = `
                            <div class="text-center p-4">
                                <p class="text-danger">Falha ao carregar os dados. Tente novamente.</p>
                            </div>`;
                    });
            }

            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                buscarEAgruparAgendamentos();
            });

            document.getElementById('btnClearFilters').addEventListener('click', () => {
                searchForm.reset();
                buscarEAgruparAgendamentos();
            });

            // Pequena função de escape para evitar injeção HTML com dados vindos do backend
            function escapeHtml(str) {
                if (str === null || str === undefined) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            // Primeira carga
            buscarEAgruparAgendamentos();
        });
    </script>
</body>
</html>
