<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Meu Portal Acadêmico</h2>
                <p class="text-muted small mb-0">Bem-vindo, {{ auth()->user()->name }}!</p>
            </div>
            <div>
                <button class="btn btn-outline-primary fw-bold shadow-sm me-2">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Baixar Histórico
                </button>
            </div>
        </div>

        @if(isset($error))
            <div class="alert alert-warning shadow-sm border-0">
                <i class="bi bi-exclamation-triangle me-2"></i> {{ $error }}
            </div>
        @else

            <!-- Card de Identificação -->
            <div class="glass-card p-4 mb-4" style="border-left: 5px solid #0d6efd;">
                <div class="row align-items-center">
                    <div class="col-md-auto mb-3 mb-md-0">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center fw-bold text-uppercase" style="width: 70px; height: 70px; font-size: 1.5rem;">
                            {{ substr($student->name, 0, 2) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4 class="fw-bold text-dark mb-1">{{ $student->name }}</h4>
                        <p class="text-muted mb-0"><i class="bi bi-person-badge me-1"></i> Matrícula: {{ str_pad($student->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-journal-check text-success me-2"></i> Meu Desempenho</h5>

            @forelse($reportCards as $report)
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="badge bg-primary px-3 py-2 fs-6 rounded-pill shadow-sm">
                            {{ $report['enrollment']->schoolClass->name }} ({{ $report['enrollment']->schoolClass->grade->name }})
                        </div>
                    </div>

                    <!-- Resumo Frequência -->
                    <div class="col-md-4">
                        <div class="glass-card p-4 text-center h-100 position-relative overflow-hidden">
                            <div class="position-absolute top-0 end-0 p-3 opacity-10">
                                <i class="bi bi-calendar-check fs-1"></i>
                            </div>
                            <h6 class="text-muted fw-bold mb-3 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Minha Frequência</h6>
                            @php
                                $freqColor = $report['global_attendance'] >= 75 ? 'success' : 'danger';
                            @endphp
                            <div class="display-4 fw-bold text-{{ $freqColor }} mb-2">
                                {{ $report['global_attendance'] }}%
                            </div>
                            <div class="text-muted small">
                                Você possui <strong>{{ $report['total_absences'] }}</strong> faltas neste ano.
                            </div>
                        </div>
                    </div>

                    <!-- Notas -->
                    <div class="col-md-8">
                        <div class="glass-card p-0 overflow-hidden h-100">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4 py-3 text-muted">Disciplina</th>
                                            <th class="text-center py-3 text-muted">Aulas</th>
                                            <th class="text-center py-3 text-muted">Faltas</th>
                                            <th class="text-center py-3 pe-4 text-muted">Nota Atual</th>
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
                <div class="alert alert-info">Você ainda não foi enturmado neste semestre. Procure a secretaria.</div>
            @endforelse

        @endif
    </div>
</x-app-layout>
