<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Cockpit Executivo</h2>
                <p class="text-muted small mb-0">Visão gerencial da unidade de ensino.</p>
            </div>
            <div>
                <button class="btn btn-primary bg-gradient shadow-sm rounded-pill px-4">
                    <i class="bi bi-cloud-download me-2"></i> Relatório PDF
                </button>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Card Alunos -->
            <div class="col-md-6 col-lg-3">
                <div class="glass-card p-4 h-100 d-flex flex-column justify-content-center position-relative overflow-hidden" style="border-left: 4px solid #3b82f6;">
                    <i class="bi bi-people-fill position-absolute text-primary opacity-10" style="font-size: 5rem; right: -10px; bottom: -15px;"></i>
                    <p class="text-muted small fw-bold text-uppercase mb-1">Alunos Ativos</p>
                    <h3 class="fw-bold mb-0 text-dark">{{ $totalStudents }}</h3>
                </div>
            </div>

            <!-- Card Professores -->
            <div class="col-md-6 col-lg-3">
                <div class="glass-card p-4 h-100 d-flex flex-column justify-content-center position-relative overflow-hidden" style="border-left: 4px solid #8b5cf6;">
                    <i class="bi bi-person-workspace position-absolute text-purple opacity-10" style="font-size: 5rem; right: -10px; bottom: -15px;"></i>
                    <p class="text-muted small fw-bold text-uppercase mb-1">Corpo Docente</p>
                    <h3 class="fw-bold mb-0 text-dark">{{ $totalTeachers }}</h3>
                </div>
            </div>

            <!-- Card Receita -->
            <div class="col-md-6 col-lg-3">
                <div class="glass-card p-4 h-100 d-flex flex-column justify-content-center position-relative overflow-hidden" style="border-left: 4px solid #10b981;">
                    <i class="bi bi-piggy-bank-fill position-absolute text-success opacity-10" style="font-size: 5rem; right: -10px; bottom: -15px;"></i>
                    <p class="text-muted small fw-bold text-uppercase mb-1">Receita Realizada (Paga)</p>
                    <h3 class="fw-bold mb-0 text-success">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</h3>
                </div>
            </div>

            <!-- Card Inadimplencia -->
            <div class="col-md-6 col-lg-3">
                <div class="glass-card p-4 h-100 d-flex flex-column justify-content-center position-relative overflow-hidden" style="border-left: 4px solid #ef4444;">
                    <i class="bi bi-exclamation-triangle-fill position-absolute text-danger opacity-10" style="font-size: 5rem; right: -10px; bottom: -15px;"></i>
                    <p class="text-muted small fw-bold text-uppercase mb-1">Inadimplência (Atrasados)</p>
                    <h3 class="fw-bold mb-0 text-danger">R$ {{ number_format($overdueRevenue, 2, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="glass-card p-4 h-100">
                    <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-graph-up text-primary me-2"></i> Desempenho Financeiro</h5>
                    <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height: 300px; border: 1px dashed #cbd5e1;">
                        <div class="text-center text-muted">
                            <i class="bi bi-bar-chart-fill fs-1 text-secondary opacity-50 mb-2"></i>
                            <p class="mb-0">Aguardando período letivo para gerar o gráfico</p>
                            <p class="small text-primary fw-bold mt-2">Valores Abertos: R$ {{ number_format($pendingRevenue, 2, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="glass-card p-4 h-100" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white;">
                    <h5 class="fw-bold mb-4 text-white"><i class="bi bi-lightning-fill text-warning me-2"></i> Ações Rápidas</h5>
                    <div class="d-grid gap-3">
                        <a href="{{ route('enrollments.index') }}" class="btn btn-outline-light text-start py-3"><i class="bi bi-person-plus-fill me-2"></i> Nova Matrícula</a>
                        <a href="{{ route('finance.invoices.index') }}" class="btn btn-outline-light text-start py-3"><i class="bi bi-cash-stack me-2"></i> Gerar Faturas</a>
                        <a href="{{ route('academic.smart.index') }}" class="btn btn-warning text-start py-3 text-dark fw-bold"><i class="bi bi-magic me-2"></i> Enturmação Inteligente</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
