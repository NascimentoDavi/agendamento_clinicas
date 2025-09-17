<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Calendário - Agendamentos Clínica - Administrador</title>

    <!-- FAVICON - IMAGEM DA GUIA -->
    <link rel="icon" type="image/png" href="/favicon_faesa.png">

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootsrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    
    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>

    <style>
        #calendar {
            width: 100%;
            height: 100vh; /* ocupa toda altura da tela */
            max-width: 900px;
            max-height: 86vh;
            overflow-y: auto;
            margin: auto;
            margin-right: 10px; 
        }

        #calendar::-webkit-scrollbar {
            width: 6px;
        }
        #calendar::-webkit-scrollbar-track {
            background: #ecf5f9;
            border-radius: 8px;
        }
        #calendar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #2596be, #7aacce);
        border-radius: 8px;
        }
        #calendar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #7aacce, #2596be);
        }

        /* Limita altura de eventos apenas na view Mês */
        .fc-dayGridMonth-view .fc-daygrid-day-events {
            max-height: 120px;
            overflow-y: auto;
        }

        /* Na view Dia, os eventos ocupam todo o espaço disponível */
        .fc-dayGridDay-view .fc-daygrid-day-events,
        .fc-timeGridDay-view .fc-timegrid-event {
            max-height: none;
            height: auto !important;
        }


        /* Esconde o scroll normalmente */
        .fc-daygrid-day-events {
            overflow-x: hidden;
            padding-right: 4px;
            scrollbar-width: none;
        }

        /* Ativa o scroll quando passar o mouse */
            .fc-daygrid-day-events:hover {
            scrollbar-width: thin;
            scrollbar-color: #2596be #ecf5f9;
        }

        /* Chrome, Edge e Safari */
        .fc-daygrid-day-events::-webkit-scrollbar {
            width: 0px; /* escondido */
        }

        /* Quando passar o mouse, mostra a barrinha */
        .fc-daygrid-day-events:hover::-webkit-scrollbar {
            width: 2px; /* largura visível */
        }

        .fc-daygrid-day-events:hover::-webkit-scrollbar-track {
            background: #ecf5f9;
            border-radius: 10px;
        }

        .fc-daygrid-day-events:hover::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #2596be, #7aacce);
            border-radius: 56px;
            border: 2px solid #ecf5f9;
        }

        .fc-daygrid-day-events:hover::-webkit-scrollbar-thumb:hover {
         background: linear-gradient(180deg, #2596be, #2596be);
        }

        .shadow-dark {
            box-shadow: 0 0.75rem 1.25rem rgba(0,0,0,0.4) !important;
        }

        /* Navbar normal */
        #mainNavbar {
            width: 240px;
            transition: width 0.3s ease;
            overflow: hidden;
        }

        /* Navbar encolhida */
        #mainNavbar.collapsed {
            width: 70px;
        }

        #mainNavbar.collapsed .nav-link span {
            display: none; /* esconde só os textos, mantém ícones */
        }
        #mainNavbar {
        width: 250px;
        background-color: var(--blue-color);
        transition: width 0.3s ease;
        overflow: hidden;
        }

        #mainNavbar.collapsed {
        width: 70px;
        }

    </style>

</head >
    
<body class="bg-body-secondary">
    
    @include('components.navbar')

    <div class="container ms-3 me-3 mw-100">
        <div class="row">

            <x-page-title>
                    <p onclick="window.location.href = '/psicologia/criar-agendamento'" class="btn btn-success p-2 me-3" style="font-size: 15px;" >
                        <span>Novo Agendamento</span>
                    </p>
            </x-page-title>

            <div class="col-12 shadow-lg shadow-dark pt-3 mt-3 mt-sm-0 bg-body-tertiary rounded">
                <!-- CALENDÁRIO -->
                <div id="calendar" style="max-width: 100%;" class="bg-light-subtle"></div>
            </div>
        </div>
    </div>

    <!-- MODAL PARA DETALHES DO AGENDAMENTO -->
    <div class="modal fade" id="agendamentoModal" tabindex="-1" aria-labelledby="agendamentoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                
            <!-- CABEÇALHO MODAL -->
                <div class="modal-header">
                    <h5 class="modal-title" id="agendamentoModalLabel">Detalhes do Agendamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- CORPO MODAL -->
                <div class="modal-body">
                    <p><strong>Paciente:</strong> <span id="modalPaciente"></span></p>
                    <p><strong>Psicólogo:</strong> <span id="modalPsicologo"></span></p>
                    <p><strong>Data e Horário:</strong> <span id="modalDataHora"></span></p>
                    <p><strong>Serviço:</strong> <span id="modalServico"></span></p>

                    <!-- STATUS AGENDAMENTO -->
                    <div class="mb-3">
                        <label for="modalStatusSelect" class="form-label"><strong>Status:</strong></label>
                        <select class="form-select" id="modalStatusSelect">
                            <option value="Agendado">Agendado</option>
                            <option value="Presente">Presente</option>
                            <option value="Cancelado">Cancelado</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>

                    <!-- CHECK PAGAMENTO -->
                    <div class="form-check mb-3" id="checkPagamentoAgendamento">
                        <input type="checkbox" class="form-check-input" id="modalCheckPagamento" name="STATUS_PAG" value="S">
                        <label for="modalCheckPagamento" class="form-check-label">Pago?</label>
                    </div>

                    <!-- VALOR PAGO -->
                    <div class="input-group mb-3 d-none" id="valorPagoAgendamento">
                        <span class="input-group-text" for="modalValorPagamento">$</span>
                        <input type="number" class="form-control" id="modalValorPagamento">
                    </div>

                    <!-- LOCAL -->
                    <p><strong>Local:</strong> <span id="modalLocal"></span></p>

                    <!-- OBSERVAÇÕES -->
                    <p><strong>Observações:</strong> <span id="modalObservacoes"></span></p>
                </div>
                
                <!-- RODAPÉ MODAL -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnSalvarStatus">Salvar Alterações</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL DE MOTIVO DE CANCELAMENTO -->
    <div class="modal fade" id="motivoCancelamentoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Motivo do Cancelamento</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="text-cancelamento" class="form-label">Motivo:</label>
                        <input type="text" class="form-control" id="text-cancelamento">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="btnMensagemCancelamento">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    
</body >

<!-- FULLCALENDAR SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
    let calendar;

    function getCalendarOptions(screenWidth) {
        return {
            initialView: screenWidth <= 600 ? "dayGridDay" : "dayGridMonth",
            contentHeight: "auto",
            expandRows: true,
            timeZone: 'local',
            headerToolbar: screenWidth <= 600
                ? {
                    left: 'prev,next',
                    center: '',
                    right: 'dayGridDay,timeGridWeek,dayGridMonth',
                }
                : {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridDay,timeGridWeek,dayGridMonth',
                },
            slotMinTime: "08:00:00",
            slotMaxTime: "20:30:00",
            businessHours: {
                daysOfWeek: [1, 2, 3, 4, 5, 6],
                startTime: "08:00",
                endTime: "23:00",
            },
            buttonText: {
                today: "Hoje",
                month: "Mês",
                week: "Semana",
                day: "Dia",
                list: "Lista",
            },
            locale: "pt-br",
            selectable: true,
            editable: false,
            select: function (info) {
                
              return;
            },
            eventDidMount: function(info) {
                info.el.style.backgroundColor = info.event.backgroundColor;
                info.el.style.borderColor = info.event.backgroundColor;
                info.el.style.color = 'white';
            },
            events: '/psicologia/agendamentos-calendar/adm',

            // EXECUTADO QUANDO O USUÁRIO CLICA EM UM EVENTO DO CALENDÁRIO
            eventClick: function(info) {
                const event = info.event;
                const start = event.start;
                const end = event.end;
                const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' };
                const dataHoraStr = start.toLocaleString('pt-BR', options) + " - " + (end ? end.toLocaleString('pt-BR', options) : '');

                document.getElementById('modalPaciente').textContent = event.title;
                document.getElementById('modalPsicologo').textContent = event.extendedProps.aluno || 'Não informado';
                document.getElementById('modalDataHora').textContent = dataHoraStr;
                document.getElementById('modalObservacoes').textContent = event.extendedProps.description || 'Nenhuma observação';
                document.getElementById('modalStatusSelect').value = event.extendedProps.status || 'Agendado';
                document.getElementById('modalLocal').textContent = event.extendedProps.local || 'Não informado';
                document.getElementById('modalServico').textContent = event.extendedProps.servico || 'Não informado';

                const checkPagamento = event.extendedProps.checkPagamento || 'N';
                const checkPagamentoEl = document.getElementById('modalCheckPagamento');
                checkPagamentoEl.checked = checkPagamento === 'S';
                checkPagamentoEl.value = checkPagamento;

                // Exibe campo de valor se estiver pago
                const valorPagamentoSection = document.getElementById('valorPagoAgendamento');
                if (checkPagamento === 'S') {
                    valorPagamentoSection.classList.remove('d-none');
                } else {
                    valorPagamentoSection.classList.add('d-none');
                }

                document.getElementById('modalValorPagamento').value = event.extendedProps.valorPagamento || '';

                const modal = new bootstrap.Modal(document.getElementById('agendamentoModal'));
                document.getElementById('btnSalvarStatus').setAttribute('data-event-id', event.id);
                document.getElementById('btnMensagemCancelamento').setAttribute('data-event-id', event.id);
                modal.show();
            },

            //  CUSTOMIZA O DISPLAY DE UM EVENTO NO CALENDÁRIO
            eventContent: function(arg) {
                if (screenWidth <= 700) {
                    return { domNodes: [document.createTextNode(arg.event.title)] };
                } else {
                    const timeText = arg.timeText + ' ';
                    return { domNodes: [document.createTextNode(timeText + arg.event.title)] };
                }
            }
        };
    }

    function renderCalendar() {
        const calendarEl = document.getElementById("calendar");

        // Destroi o calendário anterior, se já existir
        if (calendar) {
            calendar.destroy();
        }

        // Cria nova instância com base na largura atual
        const screenWidth = window.innerWidth;
        const options = getCalendarOptions(screenWidth);

        calendar = new FullCalendar.Calendar(calendarEl, options);
        calendar.render();
    }

    // RENDERIZA NA PRIMEIRA VEZ
    document.addEventListener('DOMContentLoaded', function () {
        renderCalendar();
    });

    // RECRIA O CALENDÁRIO AO REDIMENSIONAR (com debounce)
    let currentScreenWidth = window.innerWidth;

    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            const newScreenWidth = window.innerWidth;
            
            // só recria se cruzar o breakpoint de 600px
            if ((currentScreenWidth <= 600 && newScreenWidth > 600) ||
                (currentScreenWidth > 600 && newScreenWidth <= 600)) {
                currentScreenWidth = newScreenWidth;
                renderCalendar();
            }
        }, 300);
    });

    // SCRIPT PARA MUDANÇA DE STATUS DE AGENDAMENTO
    document.getElementById('btnSalvarStatus').addEventListener('click', function () {
        const eventId = this.getAttribute('data-event-id');
        // PEGA VALOR DO STATUS ATUALIZADO
        const novoStatus = document.getElementById('modalStatusSelect').value;
        // PEGA STATUS DO PAGAMENTO ATUALIZADO
        const checkPagamento = document.getElementById('modalCheckPagamento').checked ? 'S' : 'N' ;
        // PEGA VALOR DO PAGAMENTO ATUALIZADO
        const valorPagamento = document.getElementById('modalValorPagamento').value;

        if (novoStatus === "Cancelado") {
            bootstrap.Modal.getInstance(document.getElementById('agendamentoModal')).hide();
            const modal = new bootstrap.Modal((document.getElementById('motivoCancelamentoModal')));
            modal.show();
        } else {
            fetch(`/psicologia/agendamentos/${eventId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: novoStatus, checkPagamento: checkPagamento, valorPagamento: valorPagamento })
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro ao atualizar status.');
                return response.json();
            })
            .then(data => {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('agendamentoModal')).hide();
            })
            .catch(error => {
                alert('Erro: ' + error.message);
            });
        }
    });

</script>

<!-- MODAL DE CANCELAMENTO -->
<script>
    document.getElementById("btnMensagemCancelamento").addEventListener('click', function() {

        const content = document.getElementById('text-cancelamento').value;
        const eventId = this.getAttribute('data-event-id');
        const checkPagamento = document.getElementById('modalCheckPagamento').checked ? 'S' : 'N';
        const valorPagamento = document.getElementById('modalValorPagamento').value;

        // CAOS NÃO INSIRA MOTIVO
        if (!content) {
            alert("Insira um motivo para poder continuar")
        } else {
            fetch(`/psicologia/agendamentos/${eventId}/mensagem-cancelamento`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ mensagem : content, id : eventId, checkPagamento : checkPagamento, valorPagamento: valorPagamento })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao atualizar agendamento.');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('text-cancelamento').value = '';
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('motivoCancelamentoModal')).hide();
            })
            .catch(error => {
                alert('Erro: ' + error.message);
            });
        }
    })
</script>

<!-- SCRIPT PARA INPUT DE VALOR PAGO EM AGENDAMENTO | MODAL AGENDAMENTO -->
<script>
    const valorPagamentoSection = document.getElementById('valorPagoAgendamento');
    const checkPagamentoSection = document.getElementById('modalCheckPagamento');

    checkPagamentoSection.addEventListener('change', function() {
        if(this.checked) {
            valorPagamentoSection.classList.remove('d-none');
        } else {
            valorPagamentoSection.classList.add('d-none');
        }
    })
</script>




</html>
