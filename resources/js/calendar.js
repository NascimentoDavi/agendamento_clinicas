import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

let calendar;

function getCalendarOptions(screenWidth) {
    return {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: screenWidth <= 600 ? "dayGridDay" : "dayGridMonth",
        contentHeight: "auto",
        expandRows: true,
        timeZone: 'local',
        headerToolbar: screenWidth <= 600
            ? { left: 'prev,next', center: '', right: 'dayGridDay,timeGridWeek,dayGridMonth' }
            : { left: 'prev,next today', center: 'title', right: 'dayGridDay,timeGridWeek,dayGridMonth' },
        slotMinTime: "08:00:00",
        slotMaxTime: "20:30:00",
        businessHours: { daysOfWeek: [1,2,3,4,5,6], startTime: "08:00", endTime: "23:00" },
        buttonText: { today: "Hoje", month: "Mês", week: "Semana", day: "Dia", list: "Lista" },
        locale: "pt-br",
        selectable: true,
        editable: false,
        select: function () { return; },
        eventDidMount: function(info) {
            info.el.style.backgroundColor = info.event.backgroundColor;
            info.el.style.borderColor = info.event.backgroundColor;
            info.el.style.color = 'white';
        },
        events: '/psicologia/agendamentos-calendar/adm/',
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

            const valorPagamentoSection = document.getElementById('valorPagoAgendamento');
            if(checkPagamento === 'S') {
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
        eventContent: function(arg) {
            if(screenWidth <= 700) {
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
    if(!calendarEl) return;

    if(calendar) calendar.destroy();

    const screenWidth = window.innerWidth;
    const options = getCalendarOptions(screenWidth);
    calendar = new Calendar(calendarEl, options);
    calendar.render();
}

// Inicializa calendário
document.addEventListener('DOMContentLoaded', () => {
    renderCalendar();

    // Listener para alterar status
    document.getElementById('btnSalvarStatus').addEventListener('click', function() {
        const eventId = this.getAttribute('data-event-id');
        const novoStatus = document.getElementById('modalStatusSelect').value;
        const checkPagamento = document.getElementById('modalCheckPagamento').checked ? 'S' : 'N';
        const valorPagamento = document.getElementById('modalValorPagamento').value;

        if(novoStatus === "Cancelado") {
            bootstrap.Modal.getInstance(document.getElementById('agendamentoModal')).hide();
            new bootstrap.Modal(document.getElementById('motivoCancelamentoModal')).show();
        } else {
            fetch(`/psicologia/agendamentos/${eventId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: novoStatus, checkPagamento, valorPagamento })
            })
            .then(resp => { if(!resp.ok) throw new Error('Erro ao atualizar status.'); return resp.json(); })
            .then(() => {
                calendar.refetchEvents();
                bootstrap.Modal.getInstance(document.getElementById('agendamentoModal')).hide();
            })
            .catch(err => alert(err.message));
        }
    });

    // Listener para motivo de cancelamento
    document.getElementById("btnMensagemCancelamento").addEventListener('click', function() {
        const content = document.getElementById('text-cancelamento').value;
        const eventId = this.getAttribute('data-event-id');
        const checkPagamento = document.getElementById('modalCheckPagamento').checked ? 'S' : 'N';
        const valorPagamento = document.getElementById('modalValorPagamento').value;

        if(!content) return alert("Insira um motivo para poder continuar");

        fetch(`/psicologia/agendamentos/${eventId}/mensagem-cancelamento`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ mensagem: content, id: eventId, checkPagamento, valorPagamento })
        })
        .then(resp => { if(!resp.ok) throw new Error('Erro ao atualizar agendamento.'); return resp.json(); })
        .then(() => {
            document.getElementById('text-cancelamento').value = '';
            calendar.refetchEvents();
            bootstrap.Modal.getInstance(document.getElementById('motivoCancelamentoModal')).hide();
        })
        .catch(err => alert(err.message));
    });

    // Listener para mostrar/ocultar input de valor pago
    const valorSection = document.getElementById('valorPagoAgendamento');
    const checkSection = document.getElementById('modalCheckPagamento');
    checkSection.addEventListener('change', function() {
        if(this.checked) valorSection.classList.remove('d-none');
        else valorSection.classList.add('d-none');
    });
});

// Debounce de resize sensível a breakpoint
let resizeTimeout;
let currentScreenWidth = window.innerWidth;

window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
        const newWidth = window.innerWidth;
        if((currentScreenWidth <= 600 && newWidth > 600) || (currentScreenWidth > 600 && newWidth <= 600)) {
            currentScreenWidth = newWidth;
            renderCalendar();
        }
    }, 300);
});
