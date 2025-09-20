<div id="modal-alert-container" class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1056;"></div>

<!-- MODAL DE EDIÇÃO DE HORÁRIOS -->
    <div class="modal fade" id="editarHorarioModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">
                
                <form id="form-editar-horario" class="needs-validation">

                    <div class="modal-header">
                        <h5 class="modal-title">Editar Horário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- ID DO HORÁRIO CADASTRADO -->
                        <input type="hidden" id="edit-horario-id" name="ID_HORARIO" />
                        
                        <!-- DESCRIÇÃO -->
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" id="edit-horario-desc" name="DESCRICAO_HORARIO" class="form-control" required />
                        </div>
                        
                        <!-- TIPO DO HORÁRIO -->
                        <div class="mb-3">
                            <label class="form-label">Tipo de Horário</label>
                            <select id="edit-tipo-horario" name="BLOQUEADO" class="form-select" required>
                                <option value="N">Horário de Atendimento</option>
                                <option value="S">Horário Bloqueado</option>
                            </select>
                        </div>

                        <div class="row g-2">

                            <!-- DATA INICIAL -->
                            <div class="col-6 mb-3">
                                <label class="form-label">Data Inicial</label>
                                <input type="text" id="edit-data-horario-inicial" name="DATA_HORARIO_INICIAL" class="form-control" required />
                            </div>
                            
                            <!-- DATA FINAL -->
                            <div class="col-6 mb-3">
                                <label class="form-label">Data Final</label>
                                <input type="text" id="edit-data-horario-final" name="DATA_HORARIO_FINAL" class="form-control" required />
                            </div>
                            
                            <!-- HORÁRIO INICIAL -->
                            <div class="col-6 mb-3">
                                <label class="form-label">Horário Inicial</label>
                                <input type="text" id="edit-hr-horario-inicial" name="HR_HORARIO_INICIAL" class="form-control" required />
                            </div>

                            <!-- HORÁRIO FINAL -->
                            <div class="col-6 mb-3">
                                <label class="form-label">Horário Final</label>
                                <input type="text" id="edit-hr-horario-final" name="HR_HORARIO_FINAL" class="form-control" required />
                            </div>
                            
                        </div>
                        
                        <!-- OBSERVAÇÃO -->
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea id="edit-observacao" name="OBSERVACAO" class="form-control" rows="3">{{ old('OBSERVACAO') }}</textarea>
                        </div>

                    </div>
                    
                    <div class="modal-footer d-flex justify-content-between">

                        <!-- EXCLUR -->
                        <button type="button" class="btn btn-danger" id="btn-deletar-horario"><i class="bi bi-trash"></i> Excluir</button>
                        
                        <!-- BUTTONS -->
                        <div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                        
                    </div>
                    
                </form>
            </div>
        </div>
    </div>