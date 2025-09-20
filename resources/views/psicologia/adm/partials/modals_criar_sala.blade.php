<!-- MODAL DE EDIÇÃO DE SALA -->
<div class="modal fade" id="editarSalaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-editar-sala" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title">Editar Sala</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-sala-id" name="ID_SALA_CLINICA" />
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <input type="text" id="edit-sala-desc" name="DESCRICAO" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Disciplina</label>
                        <select name="DISCIPLINA" id="edit-sala-disc" class="form-select"></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select id="edit-sala-status" name="ATIVO" class="form-select" required>
                            <option value="S">Ativo</option>
                            <option value="N">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" id="btn-deletar-sala"><i class="bi bi-trash"></i> Excluir</button>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-alert-container" class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1056;"></div>