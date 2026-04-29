<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Painel Financeiro</h2>
        </div>

        <!-- Estatísticas (Overview) -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="glass-card d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 60px; height: 60px;">
                        <i class="bi bi-hourglass-split fs-3"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">A Receber / Atrasado</p>
                        <h3 class="mb-0 fw-black text-dark">R$ {{ number_format($totalPending, 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="glass-card d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 60px; height: 60px;">
                        <i class="bi bi-graph-up-arrow fs-3"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-1 fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Recebido neste mês</p>
                        <h3 class="mb-0 fw-black text-dark">R$ {{ number_format($totalReceived, 2, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('errors'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('errors')->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'student_class', 'label' => 'Aluno & Turma'],
                    ['name' => 'due_date_formatted', 'label' => 'Vencimento', 'searchable' => false],
                    ['name' => 'amount_formatted', 'label' => 'Valor', 'searchable' => false],
                    ['name' => 'status_badge', 'label' => 'Status', 'searchable' => false, 'orderable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="invoicesTable" url="{{ route('finance.invoices.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
