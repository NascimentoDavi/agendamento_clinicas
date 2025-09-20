    <div id="modal-alert-container" class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1056;"></div>


    <div class="modal fade" id="editarServicoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form-editar-servico" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Serviço</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-servico-id" name="ID_SERVICO_CLINICA" />
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" id="edit-servico-desc" name="SERVICO_CLINICA_DESC" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Disciplina</label>
                            <select name="DISCIPLINA" id="edit-servico-disc" class="form-select"></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor do Serviço</label>
                            <input type="text" id="edit-valor-servico" name="VALOR_SERVICO" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tempo de recorrência (meses)</label>
                            <input type="number" min="0" step="1" id="edit-tempo-recorrencia-meses" name="TEMPO_RECORRENCIA_MESES" class="form-control" placeholder="Ex: 6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea id="edit-observacao-servico" name="OBSERVACAO" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-danger" id="btn-deletar-servico"><i class="bi bi-trash"></i> Excluir</button>
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>