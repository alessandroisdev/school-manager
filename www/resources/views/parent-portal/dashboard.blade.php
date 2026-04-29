<x-app-layout>
    <div class="container-fluid">
        @if(isset($error))
            <div class="alert alert-danger">{{ $error }}</div>
        @else
            <!-- CABEÇALHO E MURAL DE AVISOS -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="fw-bold text-dark mb-1">Olá, {{ explode(' ', $guardian->name)[0] }}!</h2>
                    <p class="text-muted">Bem-vindo(a) ao Portal do Responsável. Acompanhe a rotina dos seus filhos.</p>
                </div>
            </div>

            @if($communications->count() > 0)
                <div class="mb-5">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-megaphone me-2 text-primary"></i> Mural de Avisos</h5>
                    <div class="row">
                        @foreach($communications as $comm)
                            <div class="col-md-4 mb-3">
                                <div class="glass-card p-3 h-100 border-start border-4 border-{{ $comm->type == 'warning' ? 'danger' : ($comm->type == 'event' ? 'success' : 'primary') }}">
                                    <h6 class="fw-bold">{{ $comm->title }}</h6>
                                    <p class="small text-muted mb-0">{{ Str::limit($comm->content, 100) }}</p>
                                    <div class="text-end mt-2"><small class="text-muted">{{ $comm->created_at->format('d/m/Y') }}</small></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- SELETOR DE FILHOS -->
            @if($students->count() > 0)
                <div class="glass-card p-3 mb-4 d-flex align-items-center bg-primary bg-opacity-10 border-0">
                    <div class="me-3"><i class="bi bi-people-fill fs-3 text-primary"></i></div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold text-dark">Visualizando dados de:</h6>
                        <form id="child-selector-form" method="GET" action="{{ route('parent.dashboard') }}">
                            <select name="student_id" class="form-select border-0 shadow-sm fw-bold text-primary" onchange="document.getElementById('child-selector-form').submit()">
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ $selectedStudent && $selectedStudent->id == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} (Matrícula: {{ $student->registration }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <!-- DADOS DO FILHO SELECIONADO -->
                @if($selectedStudent)
                    <ul class="nav nav-tabs mb-4" id="childTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="boletim-tab" data-bs-toggle="tab" data-bs-target="#boletim" type="button" role="tab"><i class="bi bi-mortarboard me-1"></i> Boletim Escolar</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-success" id="financeiro-tab" data-bs-toggle="tab" data-bs-target="#financeiro" type="button" role="tab"><i class="bi bi-currency-dollar me-1"></i> Financeiro</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="childTabsContent">
                        <!-- ABA BOLETIM -->
                        <div class="tab-pane fade show active" id="boletim" role="tabpanel">
                            @forelse($reportCards as $report)
                                <div class="row g-4 mb-4">
                                    <div class="col-12">
                                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-0">
                                            <i class="bi bi-book me-2 text-primary"></i> 
                                            {{ $report['enrollment']->schoolClass->name }} 
                                        </h5>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="glass-card p-4 text-center h-100">
                                            <h6 class="text-muted fw-bold mb-3 text-uppercase" style="font-size: 0.75rem;">Frequência Global</h6>
                                            <div class="display-4 fw-bold text-{{ $report['global_attendance'] >= 75 ? 'success' : 'danger' }} mb-2">
                                                {{ $report['global_attendance'] }}%
                                            </div>
                                            <div class="text-muted small">Total de {{ $report['total_absences'] }} faltas.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="glass-card p-0 overflow-hidden h-100">
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="ps-4 py-3">Disciplina</th>
                                                            <th class="text-center py-3">Aulas</th>
                                                            <th class="text-center py-3">Faltas</th>
                                                            <th class="text-center py-3 pe-4">Nota Acumulada</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($report['subjects'] as $subject)
                                                            <tr>
                                                                <td class="ps-4 fw-bold text-dark py-3">{{ $subject['subject_name'] }}</td>
                                                                <td class="text-center py-3 text-muted">{{ $subject['lessons_count'] }}</td>
                                                                <td class="text-center py-3"><span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">{{ $subject['absences'] ?: '-' }}</span></td>
                                                                <td class="text-center py-3 pe-4"><span class="fw-bold text-{{ $subject['total_score'] >= 6 ? 'success' : 'danger' }} fs-5">{{ number_format($subject['total_score'], 1, ',', '.') }}</span></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="glass-card p-4 text-center text-muted">Nenhum boletim disponível no momento.</div>
                            @endforelse
                        </div>

                        <!-- ABA FINANCEIRO -->
                        <div class="tab-pane fade" id="financeiro" role="tabpanel">
                            <div class="glass-card p-4">
                                <h5 class="fw-bold text-dark mb-4">Mensalidades e Faturas</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Ref. Parcela</th>
                                                <th>Vencimento</th>
                                                <th>Valor</th>
                                                <th>Status</th>
                                                <th class="text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($invoices as $invoice)
                                                <tr>
                                                    <td class="fw-bold text-dark">{{ str_pad($invoice->installment_number ?? 1, 2, '0', STR_PAD_LEFT) }}/12</td>
                                                    <td>
                                                        {{ $invoice->due_date->format('d/m/Y') }}
                                                        @if($invoice->status == 'pending' && $invoice->due_date < now())
                                                            <span class="badge bg-danger ms-1">Atrasado</span>
                                                        @endif
                                                    </td>
                                                    <td class="fw-bold text-dark">R$ {{ number_format($invoice->amount, 2, ',', '.') }}</td>
                                                    <td>
                                                        @if($invoice->status == 'paid')
                                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">Pago</span>
                                                        @else
                                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">Pendente</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if($invoice->status != 'cancelled')
                                                            <a href="{{ route('finance.boleto.show', $invoice) }}" target="_blank" class="btn btn-sm btn-outline-success fw-bold shadow-sm">
                                                                <i class="bi bi-upc-scan me-1"></i> Imprimir Boleto/Pix
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-4 text-muted">Não há registros financeiros para este aluno.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="glass-card p-5 text-center text-muted">
                    <i class="bi bi-person-x fs-1 mb-3 d-block"></i>
                    Nenhum aluno vinculado ao seu perfil. Por favor, procure a secretaria.
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
