<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-0 text-dark fw-bold">Mala Direta (Lotes Financeiros)</h2>
                <p class="text-muted mb-0">Acompanhe o status do envio de e-mails e a geração dos arquivos de Gráfica (ZIP).</p>
            </div>
            <div>
                <a href="{{ route('finance.invoices.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Voltar ao Faturamento
                </a>
            </div>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'name', 'label' => 'Campanha'],
                    ['name' => 'created_at', 'label' => 'Data Criação', 'searchable' => false],
                    ['name' => 'total_items', 'label' => 'Itens', 'searchable' => false],
                    ['name' => 'status_badge', 'label' => 'Status', 'searchable' => false],
                    ['name' => 'progress', 'label' => 'Progresso', 'searchable' => false, 'orderable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="campaignsTable" url="{{ route('finance.invoice-campaigns.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
