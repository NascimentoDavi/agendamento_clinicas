<!-- MODAL PARA DETALHES DO AGENDAMENTO -->
<div class="modal fade" id="agendamentoModal" tabindex="-1" aria-labelledby="agendamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agendamentoModalLabel">Detalhes do Agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p><strong>Paciente:</strong> <span id="modalPaciente"></span></p>
                <p><strong>Psicólogo:</strong> <span id="modalPsicologo"></span></p>
                <p><strong>Data e Horário:</strong> <span id="modalDataHora"></span></p>
                <p><strong>Serviço:</strong> <span id="modalServico"></span></p>

                <div class="mb-3">
                    <label for="modalStatusSelect" class="form-label"><strong>Status:</strong></label>
                    <select class="form-select" id="modalStatusSelect">
                        <option value="Agendado">Agendado</option>
                        <option value="Presente">Presente</option>
                        <option value="Cancelado">Cancelado</option>
                        <option value="Finalizado">Finalizado</option>
                    </select>
                </div>

                <div class="form-check mb-3" id="checkPagamentoAgendamento">
                    <input type="checkbox" class="form-check-input" id="modalCheckPagamento" name="STATUS_PAG" value="S">
                    <label for="modalCheckPagamento" class="form-check-label">Pago?</label>
                </div>

                <div class="input-group mb-3 d-none" id="valorPagoAgendamento">
                    <span class="input-group-text" for="modalValorPagamento">$</span>
                    <input type="number" class="form-control" id="modalValorPagamento">
                </div>

                <p><strong>Local:</strong> <span id="modalLocal"></span></p>
                <p><strong>Observações:</strong> <span id="modalObservacoes"></span></p>
            </div>
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
