<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-0 text-dark fw-bold">Protocolos (Recebimento)</h2>
                <p class="text-muted small mb-0">Caixa de entrada de Ofícios, Processos e Solicitações</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#newProtocolModal">
                    <i class="bi bi-plus-lg me-1"></i> Receber Documento
                </button>
            </div>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'protocol_info', 'label' => 'Protocolo / Assunto'],
                    ['name' => 'dates', 'label' => 'Datas (Entrada / Prazo)', 'searchable' => false],
                    ['name' => 'status_badge', 'label' => 'Status', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="protocolsTable" url="{{ route('secretariat.protocols.index') }}" :columns="$columns" />
        </div>
    </div>

    <!-- Modal Novo Protocolo -->
    <div class="modal fade" id="newProtocolModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('secretariat.protocols.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Registrar Entrada de Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Remetente (Órgão / Pessoa) *</label>
                            <input type="text" name="sender" class="form-control bg-light border-0" required placeholder="Ex: Secretaria de Educação">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Assunto principal *</label>
                            <input type="text" name="subject" class="form-control bg-light border-0" required placeholder="Ex: Solicitação de Histórico">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Data de Recebimento *</label>
                            <input type="date" name="received_date" class="form-control bg-light border-0" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Prazo de Resposta</label>
                            <input type="date" name="due_date" class="form-control bg-light border-0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small">Prioridade</label>
                            <select name="priority" class="form-select bg-light border-0">
                                <option value="low">Baixa</option>
                                <option value="medium" selected>Média</option>
                                <option value="high">Alta (Urgente)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted small">Observações iniciais</label>
                            <textarea name="description" class="form-control bg-light border-0" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted small">Anexar Arquivos (PDF, Imagens)</label>
                            <input type="file" name="attachments[]" class="form-control bg-light border-0" multiple accept=".pdf,.jpg,.jpeg,.png,.docx">
                            <small class="text-muted">Você pode selecionar múltiplos arquivos. Limite: 10MB por arquivo.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light fw-bold text-muted" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">Gerar Protocolo</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
