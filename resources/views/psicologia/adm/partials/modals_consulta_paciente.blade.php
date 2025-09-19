<div class="modal fade" id="confirmEditModal" tabindex="-1" aria-labelledby="confirmEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmEditModalLabel">Editar Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p><strong id="modal-paciente-nome"></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirm-edit-btn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Inativar Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <strong id="modal-delete-nome"></strong>
                <p class="text-danger small mt-2"><i class="bi bi-exclamation-triangle-fill"></i> Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn">Inativar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmAtivarModal" tabindex="-1" aria-labelledby="confirmAtivarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmAtivarModalLabel">Reativar Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <strong id="modal-ativar-nome"></strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="confirm-ativar-btn">Reativar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editPacienteModal" tabindex="-1" aria-labelledby="editPacienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <form id="editPacienteForm">

                    <!-- INFORMAÇÕES PESSOAIS PACIENTE -->
                    <h6>Informações Pessoais</h6>
                    <hr>

                    <div class="row g-3">

                        <div class="col-md-6 form-floating">
                            <input type="text" class="form-control" id="editPacienteNome" name="nome" placeholder="Nome"  value="{{ old('name') }}">
                            <label for="editPacienteNome">Nome</label>
                        </div>

                        <div class="col-md-6 form-floating">
                            <input type="text" class="form-control" id="editPacienteStatus" name="status" readonly>
                            <label for="editPacienteStatus">Status</label>
                        </div>

                        <div class="col-md-6 form-floating">
                            <input type="text" class="form-control" id="editPacienteCPF" name="cpf" placeholder="CPF">
                            <label for="editPacienteCPF">CPF</label>
                        </div>

                        <div class="col-md-6 form-floating">
                            <input type="text" class="form-control" id="editPacienteDTNASC" name="dt_nasc" placeholder="Data de Nascimento">
                            <label for="editPacienteDTNASC">Data de Nascimento</label>
                        </div>

                        <div class="col-md-6 form-floating">
                            <select name="sexo" id="editPacienteSEXO" class="form-select" aria-label="Sexo">
                                <option value="" selected>Selecione</option>
                                <option value="M">Masculino</option>
                                <option value="F">Feminino</option>
                                <option value="O">Outro</option>
                            </select>
                            <label for="editPacienteSEXO">Sexo</label>
                        </div>

                    </div>

                    <!-- INFORMAÇÕES DO RESPONSÁVEL -->
                    <h6 class="mt-3">Informações do Responsável</h6>
                    <hr>

                        <div class="row g-3">

                        <div class="col-md-6 form-floating">
                            <input type="text" class="form-control" id="editPacienteResponsavelNome" name="nome_responsavel" placeholder="Nome do Responsável" value="{{ old('nome_responsavel') }}">
                            <label for="editPacienteResponsavelNome">Nome do Responsável</label>
                        </div>

                        <div class="col-md-6 form-floating">
                            <input type="text" class="form-control" id="editPacienteResponsavelCPF" name="cpf_responsavel" placeholder="CPF do Responsável">
                            <label for="editPacienteResponsavelCPF">CPF do Responsável</label>
                        </div>

                    </div>

                    <!-- INFORMAÇÕES DE ENDEREÇO -->
                    <h6 class="mt-4">Endereço</h6>
                    <hr>

                    <div class="row g-3">

                        <div class="col-md-4 form-floating">
                            <input type="text" class="form-control" id="editPacienteCEP" name="cep" placeholder="CEP">
                            <label for="editPacienteCEP">CEP</label>
                        </div>

                        <div class="col-md-8 form-floating">
                            <input type="text" class="form-control" id="editPacienteENDERECO" name="endereco" placeholder="Rua">
                            <label for="editPacienteENDERECO">Rua</label>
                        </div>

                        <div class="col-md-4 form-floating">
                            <input type="text" class="form-control" id="editPacienteNUM" name="num" placeholder="Número">
                            <label for="editPacienteNUM">Número</label>
                        </div>

                        <div class="col-md-8 form-floating">
                            <input type="text" class="form-control" id="editPacienteCOMPLEMENTO" name="complemento" placeholder="Complemento">
                            <label for="editPacienteCOMPLEMENTO">Complemento</label>
                        </div>

                        <div class="col-md-6 form-floating">
                            <input type="text" class="form-control" id="editPacienteBAIRRO" name="bairro" placeholder="Bairro">
                            <label for="editPacienteBAIRRO">Bairro</label>
                        </div>

                        <div class="col-md-6 form-floating">
                            <input type="text" id="editPacienteMUNICIPIO" name="municipio" class="form-control" placeholder="Município">
                            <label for="editPacienteMUNICIPIO">Município</label>
                        </div>

                        <div class="col-md-2 form-floating">
                            <input type="text" class="form-control" id="editPacienteUF" name="uf" placeholder="UF">
                            <label for="editPacienteUF">UF</label>
                        </div>

                    </div>

                    <!-- INFORMAÇÕES DE CONTATO -->
                    <h6 class="mt-4">Contato</h6>
                    <hr>

                    <div class="row g-3">

                        <div class="col-md-6 form-floating">
                            <input type="text" id="editPacienteCELULAR" name="celular" class="form-control" placeholder="Celular" />
                            <label for="editPacienteCELULAR">Celular</label>
                        </div>

                        <div class="col-md-6 form-floating">
                            <input type="email" id="editPacienteEMAIL" name="email" class="form-control" placeholder="Email" />
                            <label for="editPacienteEMAIL">Email</label>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="historicoPacienteModal" tabindex="-1" aria-labelledby="historicoPacienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="historicoPacienteModalLabel">
                    <i class="bi bi-calendar-check-fill me-2"></i> Histórico de Agendamentos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 p-2 bg-light border rounded">
                    <strong>Paciente:</strong> <span id="nomePacienteHistorico"></span>
                </div>

                <div id="historicoLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2">Buscando histórico...</p>
                </div>

                <div id="historicoVazio" class="text-center py-5 d-none">
                    <i class="bi bi-emoji-frown fs-1 text-muted"></i>
                    <p class="mt-2 fs-5 text-muted">Nenhum agendamento encontrado para este paciente.</p>
                </div>

                <div id="tabelaHistoricoWrapper" class="table-responsive d-none">
                    <table class="table table-striped table-hover table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Data</th>
                                <th scope="col">Início</th>
                                <th scope="col">Fim</th>
                                <th scope="col">Serviço</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody id="historicoAgendamentosBody">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>