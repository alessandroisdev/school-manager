<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Central do Aluno</h2>
                <p class="text-muted small mb-0">Gestão completa de dados, histórico acadêmico e financeiro.</p>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-primary fw-bold shadow-sm me-2">
                    <i class="bi bi-printer me-1"></i> Imprimir
                </button>
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        <!-- Cabeçalho do Boletim -->
        <div class="glass-card p-4 mb-4" style="border-top: 5px solid #0d6efd;">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center fw-bold text-uppercase" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ substr($student->name, 0, 2) }}
                    </div>
                </div>
                <div class="col-md-6">
                    <h3 class="fw-bold text-dark mb-1">{{ $student->name }}</h3>
                    <p class="text-muted mb-1"><i class="bi bi-person-vcard me-1"></i> <strong>Documento:</strong> {{ $student->document }}</p>
                    <p class="text-muted mb-0"><i class="bi bi-envelope me-1"></i> {{ $student->email ?? 'Sem e-mail cadastrado' }}</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25 fs-6 mb-2">
                        Aluno Ativo
                    </span>
                    <div class="small text-muted"><strong>Nascimento:</strong> {{ $student->birth_date ? $student->birth_date->format('d/m/Y') : '-' }}</div>
                </div>
            </div>
        </div>

        <!-- Navegação em Abas -->
        <ul class="nav nav-tabs mb-4 d-print-none" id="studentTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-primary" id="dados-tab" data-bs-toggle="tab" data-bs-target="#dados" type="button" role="tab" aria-controls="dados" aria-selected="true"><i class="bi bi-person-lines-fill me-1"></i> Dados e Matrícula</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="boletim-tab" data-bs-toggle="tab" data-bs-target="#boletim" type="button" role="tab" aria-controls="boletim" aria-selected="false"><i class="bi bi-mortarboard me-1"></i> Histórico Acadêmico</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab" aria-controls="documentos" aria-selected="false"><i class="bi bi-file-earmark-text me-1"></i> Documentos Emitidos</button>
            </li>
            @if($unitSettings && $unitSettings->school_type === 'private')
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-success" id="financeiro-tab" data-bs-toggle="tab" data-bs-target="#financeiro" type="button" role="tab" aria-controls="financeiro" aria-selected="false"><i class="bi bi-currency-dollar me-1"></i> Financeiro</button>
            </li>
            @endif
        </ul>

        <div class="tab-content" id="studentTabsContent">
            <!-- ABA DADOS E EDIÇÃO -->
            <div class="tab-pane fade show active d-print-none" id="dados" role="tabpanel" aria-labelledby="dados-tab">
                <form action="{{ route('students.update', $student) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <!-- Coluna Principal: Dados Pessoais e Endereço -->
                        <div class="col-lg-8">
                            
                            <!-- Dados Pessoais -->
                            <div class="glass-card p-4 mb-4">
                                <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-person-badge me-2"></i> Dados Pessoais</h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="name" class="form-label fw-bold text-muted small">Nome Completo do Aluno *</label>
                                        <input type="text" name="name" id="name" class="form-control form-control-lg bg-light border-0 @error('name') is-invalid @enderror" value="{{ old('name', $student->name) }}" required placeholder="Ex: João da Silva">
                                    </div>
        
                                    <div class="col-md-6">
                                        <label for="document" class="form-label fw-bold text-muted small">Documento (CPF ou RG) *</label>
                                        <input type="text" name="document" id="document" class="form-control bg-light border-0 @error('document') is-invalid @enderror" value="{{ old('document', $student->document) }}" required placeholder="000.000.000-00">
                                    </div>
        
                                    <div class="col-md-6">
                                        <label for="birth_date" class="form-label fw-bold text-muted small">Data de Nascimento *</label>
                                        <input type="date" name="birth_date" id="birth_date" class="form-control bg-light border-0 @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', $student->birth_date ? $student->birth_date->format('Y-m-d') : '') }}" required>
                                    </div>
        
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label fw-bold text-muted small">Gênero Identificado</label>
                                        <select name="gender" id="gender" class="form-select bg-light border-0 @error('gender') is-invalid @enderror">
                                            <option value="">Selecione...</option>
                                            <option value="Masculino" {{ old('gender', $student->gender) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                            <option value="Feminino" {{ old('gender', $student->gender) == 'Feminino' ? 'selected' : '' }}>Feminino</option>
                                            <option value="Outro" {{ old('gender', $student->gender) == 'Outro' ? 'selected' : '' }}>Outro</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="status" class="form-label fw-bold text-muted small">Status Institucional</label>
                                        <select name="status" id="status" class="form-select bg-light border-0 @error('status') is-invalid @enderror">
                                            <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Matrícula Ativa</option>
                                            <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inativo</option>
                                            <option value="transferred" {{ old('status', $student->status) == 'transferred' ? 'selected' : '' }}>Transferido</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
        
                            <!-- Endereço -->
                            <div class="glass-card p-4">
                                <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-geo-alt me-2"></i> Endereço Residencial</h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="address_zipcode" class="form-label fw-bold text-muted small">CEP</label>
                                        <input type="text" name="address_zipcode" id="address_zipcode" class="form-control bg-light border-0" value="{{ old('address_zipcode', $student->address_zipcode) }}" placeholder="00000-000">
                                    </div>
                                    <div class="col-md-8">
                                        <label for="address_street" class="form-label fw-bold text-muted small">Logradouro</label>
                                        <input type="text" name="address_street" id="address_street" class="form-control bg-light border-0" value="{{ old('address_street', $student->address_street) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="address_number" class="form-label fw-bold text-muted small">Número</label>
                                        <input type="text" name="address_number" id="address_number" class="form-control bg-light border-0" value="{{ old('address_number', $student->address_number) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="address_neighborhood" class="form-label fw-bold text-muted small">Bairro</label>
                                        <input type="text" name="address_neighborhood" id="address_neighborhood" class="form-control bg-light border-0" value="{{ old('address_neighborhood', $student->address_neighborhood) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="address_city" class="form-label fw-bold text-muted small">Cidade</label>
                                        <input type="text" name="address_city" id="address_city" class="form-control bg-light border-0" value="{{ old('address_city', $student->address_city) }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="address_state" class="form-label fw-bold text-muted small">UF</label>
                                        <input type="text" name="address_state" id="address_state" class="form-control bg-light border-0" value="{{ old('address_state', $student->address_state) }}" maxlength="2">
                                    </div>
                                </div>
                            </div>
                        </div>
        
                        <!-- Coluna Lateral: Contato e Médico -->
                        <div class="col-lg-4">
                            <!-- Contato -->
                            <div class="glass-card p-4 mb-4">
                                <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-telephone me-2"></i> Contato Direto</h5>
                                <div class="mb-3">
                                    <label for="phone" class="form-label fw-bold text-muted small">Telefone Celular</label>
                                    <input type="text" name="phone" id="phone" class="form-control bg-light border-0" value="{{ old('phone', $student->phone) }}" placeholder="(00) 90000-0000">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold text-muted small">E-mail do Aluno/Responsável</label>
                                    <input type="email" name="email" id="email" class="form-control bg-light border-0" value="{{ old('email', $student->email) }}">
                                </div>
                            </div>
        
                            <!-- Ficha Médica -->
                            <div class="glass-card p-4 mb-4 border-danger border-opacity-25">
                                <h5 class="fw-bold mb-4 text-danger"><i class="bi bi-heart-pulse me-2"></i> Ficha Médica</h5>
                                <div class="mb-3">
                                    <label for="blood_type" class="form-label fw-bold text-muted small">Tipo Sanguíneo</label>
                                    <select name="blood_type" id="blood_type" class="form-select bg-light border-0">
                                        <option value="">Não Informado</option>
                                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt)
                                            <option value="{{ $bt }}" {{ old('blood_type', $student->blood_type) == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="medical_notes" class="form-label fw-bold text-muted small">Alergias e Condições</label>
                                    <textarea name="medical_notes" id="medical_notes" rows="3" class="form-control bg-light border-0">{{ old('medical_notes', $student->medical_notes) }}</textarea>
                                </div>
                            </div>
        
                            @if($activeEnrollment)
                            <div class="glass-card p-4 mb-4 border-success border-opacity-25">
                                <h5 class="fw-bold mb-4 text-success"><i class="bi bi-currency-dollar me-2"></i> Financeiro (Matrícula Ativa)</h5>
                                
                                <div class="mb-3">
                                    <label for="bank_account_id" class="form-label fw-bold text-muted small">Banco Específico (Opcional)</label>
                                    <select name="bank_account_id" id="bank_account_id" class="form-select bg-light border-0">
                                        <option value="">Utilizar Banco Padrão</option>
                                        @foreach($bankAccounts as $bank)
                                            <option value="{{ $bank->id }}" {{ old('bank_account_id', $activeEnrollment->bank_account_id) == $bank->id ? 'selected' : '' }}>
                                                {{ $bank->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
        
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="discount_percentage" class="form-label fw-bold text-muted small">Bolsa (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" name="discount_percentage" id="discount_percentage" class="form-control bg-light border-0" value="{{ old('discount_percentage', $activeEnrollment->discount_percentage) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="discount_amount" class="form-label fw-bold text-muted small">Desc. Fixo (R$)</label>
                                        <input type="number" step="0.01" min="0" name="discount_amount" id="discount_amount" class="form-control bg-light border-0" value="{{ old('discount_amount', $activeEnrollment->discount_amount) }}">
                                    </div>
                                </div>
                            </div>
                            @endif
        
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                                    <i class="bi bi-check-circle me-2"></i> Salvar Alterações
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ABA BOLETIM -->
            <div class="tab-pane fade" id="boletim" role="tabpanel" aria-labelledby="boletim-tab">
                @forelse($reportCards as $report)
                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            <h5 class="fw-bold text-dark border-bottom pb-2 mb-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-mortarboard me-2 text-primary"></i> 
                                    {{ $report['enrollment']->schoolClass->name }} 
                                    <span class="small text-muted fw-normal ms-2">({{ $report['enrollment']->schoolClass->grade->name }} / {{ $report['enrollment']->schoolClass->shift->name }})</span>
                                </div>
                                @if(in_array($report['enrollment']->status, ['active', 'ativa']))
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fs-6">Matrícula Ativa</span>
                                @elseif(in_array($report['enrollment']->status, ['completed', 'concluida']))
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 fs-6">Ano Concluído</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fs-6">Transferido/Inativo</span>
                                @endif
                            </h5>
                        </div>

                        <!-- Resumo -->
                        <div class="col-md-4">
                            <div class="glass-card p-4 text-center h-100">
                                <h6 class="text-muted fw-bold mb-3 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Frequência Global</h6>
                                @php
                                    $freqColor = $report['global_attendance'] >= 75 ? 'success' : 'danger';
                                @endphp
                                <div class="display-4 fw-bold text-{{ $freqColor }} mb-2">
                                    {{ $report['global_attendance'] }}%
                                </div>
                                <div class="text-muted small">
                                    Total de <strong>{{ $report['total_absences'] }}</strong> faltas no período.
                                </div>
                            </div>
                        </div>

                        <!-- Matriz Curricular -->
                        <div class="col-md-8">
                            <div class="glass-card p-0 overflow-hidden h-100">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4 py-3">Disciplina</th>
                                                <th class="text-center py-3">Aulas Dadas</th>
                                                <th class="text-center py-3">Faltas</th>
                                                <th class="text-center py-3 pe-4">Nota Acumulada</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($report['subjects'] as $subject)
                                                <tr>
                                                    <td class="ps-4 fw-bold text-dark py-3">
                                                        {{ $subject['subject_name'] }}
                                                    </td>
                                                    <td class="text-center py-3 text-muted">
                                                        {{ $subject['lessons_count'] }}
                                                    </td>
                                                    <td class="text-center py-3">
                                                        @if($subject['absences'] > 0)
                                                            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">{{ $subject['absences'] }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center py-3 pe-4">
                                                        @php
                                                            $scoreColor = $subject['total_score'] >= 6.0 ? 'success' : ($subject['total_score'] > 0 ? 'warning' : 'secondary');
                                                        @endphp
                                                        <span class="fw-bold text-{{ $scoreColor }} fs-5">
                                                            {{ number_format($subject['total_score'], 1, ',', '.') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="glass-card p-5 text-center">
                        <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="fw-bold text-dark">Nenhuma matrícula ativa</h5>
                        <p class="text-muted mb-0">Este aluno não está enturmado em nenhuma classe neste ano letivo.</p>
                    </div>
                @endforelse
            </div>

            <!-- ABA DOCUMENTOS -->
            <div class="tab-pane fade d-print-none" id="documentos" role="tabpanel" aria-labelledby="documentos-tab">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Documentos Gerados</h5>
                            <p class="text-muted small mb-0">Histórico inalterável de contratos, recibos e certificados.</p>
                        </div>
                        <button type="button" class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalEmitirDocumento">
                            <i class="bi bi-plus-lg me-1"></i> Emitir Novo
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Cód. Ref.</th>
                                    <th>Template</th>
                                    <th>Data de Emissão</th>
                                    <th>Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($issuedDocuments as $doc)
                                    <tr class="{{ $doc->status !== 'valid' ? 'text-muted' : '' }}">
                                        <td class="fw-bold text-primary">{{ $doc->reference_code }}</td>
                                        <td>{{ $doc->template ? $doc->template->name : 'Template Removido' }}</td>
                                        <td>{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($doc->status === 'valid')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">Válido</span>
                                            @elseif($doc->status === 'rectified')
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">Retificado</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">Cancelado</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('issued-documents.show', $doc) }}" target="_blank" class="btn btn-sm btn-light text-primary me-1" title="Ver PDF">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                            @if($doc->status === 'valid')
                                                <form action="{{ route('issued-documents.rectify', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('Isso invalidará este documento e gerará um NOVO contrato atualizado. Tem certeza?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-light text-warning me-1" title="Retificar (Corrigir)">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('issued-documents.cancel', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('O documento ficará permanentemente marcado como cancelado. Continuar?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-light text-danger" title="Cancelar">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Nenhum documento emitido para este aluno.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- ABA FINANCEIRO -->
            @if($unitSettings && $unitSettings->school_type === 'private')
            <div class="tab-pane fade d-print-none" id="financeiro" role="tabpanel" aria-labelledby="financeiro-tab">
                <div class="glass-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Histórico Financeiro</h5>
                            <p class="text-muted small mb-0">Controle de faturas, boletos e pagamentos do aluno.</p>
                        </div>
                        <form action="{{ route('finance.carnet.generate') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <button type="submit" class="btn btn-success shadow-sm fw-bold">
                                <i class="bi bi-journal-plus me-1"></i> Gerar Carnê Anual
                            </button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Parcela</th>
                                    <th>Vencimento</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Pagamento</th>
                                    <th class="text-end">Boleto / Pix</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr>
                                        <td class="fw-bold">{{ str_pad($invoice->installment_number ?? 1, 2, '0', STR_PAD_LEFT) }}/12</td>
                                        <td>{{ $invoice->due_date->format('d/m/Y') }}
                                            @if($invoice->status == 'pending' && $invoice->due_date < now())
                                                <span class="badge bg-danger ms-1">Atrasado</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-dark">R$ {{ number_format($invoice->amount, 2, ',', '.') }}</td>
                                        <td>
                                            @if($invoice->status == 'paid')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">Pago</span>
                                            @elseif($invoice->status == 'cancelled')
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">Cancelado</span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">Pendente</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">
                                            {{ $invoice->paid_at ? $invoice->paid_at->format('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td class="text-end">
                                            @if($invoice->status != 'cancelled')
                                                <a href="{{ route('finance.boleto.show', $invoice) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Imprimir Boleto">
                                                    <i class="bi bi-upc-scan"></i> Visualizar Boleto
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Nenhuma fatura ou carnê gerado para este aluno.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Emissão Documento -->
    <div class="modal fade" id="modalEmitirDocumento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('issued-documents.store') }}" method="POST" class="modal-content">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Emitir Documento Oficial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i> Um documento oficial, após emitido, não pode ser editado. Qualquer erro exigirá uma retificação.
                    </div>
                    <label class="form-label fw-bold text-muted small">Selecione o Template (Contrato / Recibo)</label>
                    <select name="document_template_id" class="form-select bg-light border-0" required>
                        <option value="">-- Escolha um modelo --</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light fw-bold text-muted" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">Gerar PDF Congelado</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Adiciona CSS Print para melhorar a versão impressa do Boletim -->
    <style>
        @media print {
            body { background: white !important; }
            .glass-card { 
                box-shadow: none !important; 
                border: 1px solid #dee2e6 !important; 
                background: white !important; 
            }
            .sidebar, .navbar { display: none !important; }
            .content-wrapper { margin-left: 0 !important; padding: 0 !important; }
        }
    </style>
</x-app-layout>
