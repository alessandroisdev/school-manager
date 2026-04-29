<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold"><i class="bi bi-bank me-2 text-primary"></i> Contas Bancárias</h2>
            <a href="{{ route('finance.accounts.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nova Conta
            </a>
        </div>

        <div class="glass-card p-0 overflow-hidden">
            <div class="p-4 bg-light border-bottom">
                <h5 class="fw-bold mb-0 text-dark">Integrações Bancárias para Faturamento</h5>
            </div>
            <div class="p-4">
                @php
                    $columns = [
                        ['name' => 'name', 'label' => 'Nome de Identificação'],
                        ['name' => 'bank_code', 'label' => 'Código do Banco'],
                        ['name' => 'agency', 'label' => 'Agência'],
                        ['name' => 'account', 'label' => 'Conta'],
                        ['name' => 'status_badge', 'label' => 'Status', 'searchable' => false],
                        ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                    ];
                @endphp
                <x-datatable id="bankAccountsTable" url="{{ route('finance.accounts.index') }}" :columns="$columns" />
            </div>
        </div>
    </div>
</x-app-layout>