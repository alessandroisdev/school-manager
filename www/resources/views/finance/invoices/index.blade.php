<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Painel Financeiro</h2>
            <form action="{{ route('finance.carnet.batch-generate') }}" method="POST" class="d-inline" onsubmit="return confirm('Isso gerará os carnês de todas as turmas que possuem Precificação cadastrada para todos os alunos ativos que ainda não possuem carnê. Deseja continuar?');">
                @csrf
                <button type="submit" class="btn btn-primary fw-bold shadow-sm">
                    <i class="bi bi-file-earmark-plus me-1"></i> Gerar Faturas em Lote
                </button>
            </form>
        </div>

        <!-- Indicadores Financeiros (Cards) -->
        <div class="row g-4 mb-4">
            <!-- Total Pendente / Atrasado -->
            <div class="col-md-6 col-lg-4">
                <div class="glass-card p-4 border-start border-warning border-4 h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-exclamation-triangle-fill fs-1 text-warning"></i>
                    </div>
                    <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Inadimplência / Pendente</h6>
                    <h3 class="fw-bold text-dark mb-0">R$ {{ number_format($totalPending, 2, ',', '.') }}</h3>
                    <div class="small text-muted mt-2">Soma de todas as faturas não pagas</div>
                </div>
            </div>

            <!-- Total Recebido no Mês -->
            <div class="col-md-6 col-lg-4">
                <div class="glass-card p-4 border-start border-success border-4 h-100 position-relative overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-cash-stack fs-1 text-success"></i>
                    </div>
                    <h6 class="text-muted fw-bold mb-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Receita do Mês</h6>
                    <h3 class="fw-bold text-success mb-0">R$ {{ number_format($totalReceived, 2, ',', '.') }}</h3>
                    <div class="small text-muted mt-2">Pagamentos liquidados em {{ now()->translatedFormat('F/Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Tabela de Faturas -->
        <div class="glass-card p-0 overflow-hidden">
            <div class="p-4 bg-light border-bottom">
                <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-receipt me-2"></i> Mensalidades e Cobranças</h5>
            </div>
            <div class="p-4">
                @php
                    $columns = [
                        ['name' => 'student_class', 'label' => 'Aluno e Turma'],
                        ['name' => 'due_date_formatted', 'label' => 'Vencimento', 'searchable' => false],
                        ['name' => 'amount_formatted', 'label' => 'Valor', 'searchable' => false],
                        ['name' => 'status_badge', 'label' => 'Status', 'searchable' => false],
                        ['name' => 'actions', 'label' => 'Pagamento', 'orderable' => false, 'searchable' => false]
                    ];
                @endphp
                
                <x-datatable id="invoicesTable" url="{{ route('finance.invoices.index') }}" :columns="$columns" />
            </div>
        </div>
    </div>
</x-app-layout>
