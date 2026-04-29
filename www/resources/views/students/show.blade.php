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

        @forelse($reportCards as $report)
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-0">
                        <i class="bi bi-mortarboard me-2 text-primary"></i> 
                        {{ $report['enrollment']->schoolClass->name }} 
                        <span class="small text-muted fw-normal ms-2">({{ $report['enrollment']->schoolClass->grade->name }} / {{ $report['enrollment']->schoolClass->shift->name }})</span>
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
