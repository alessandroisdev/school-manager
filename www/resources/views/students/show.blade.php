<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Boletim Escolar</h2>
                <p class="text-muted small mb-0">Visão acadêmica consolidada</p>
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
                <button class="nav-link active fw-bold" id="boletim-tab" data-bs-toggle="tab" data-bs-target="#boletim" type="button" role="tab" aria-controls="boletim" aria-selected="true"><i class="bi bi-mortarboard me-1"></i> Desempenho Acadêmico</button>
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
            <!-- ABA BOLETIM -->
            <div class="tab-pane fade show active" id="boletim" role="tabpanel" aria-labelledby="boletim-tab">
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
