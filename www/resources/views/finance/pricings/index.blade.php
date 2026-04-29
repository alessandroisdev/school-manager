<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold"><i class="bi bi-tags me-2 text-primary"></i> Precificação de Turmas
            </h2>
            <a href="{{ route('finance.pricings.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nova Regra
            </a>
        </div>

        <div class="glass-card p-0 overflow-hidden">
            <div class="p-4 bg-light border-bottom">
                <h5 class="fw-bold mb-0 text-dark">Valores Anuais por Série e Turno</h5>
            </div>
            <div class="p-4">
                @php
                    $columns = [
                        ['name' => 'grade_shift', 'label' => 'Série / Turno'],
                        ['name' => 'annual_amount_formatted', 'label' => 'Valor Anual', 'searchable' => false],
                        ['name' => 'installments_info', 'label' => 'Parcelamento', 'searchable' => false],
                        ['name' => 'default_due_day', 'label' => 'Venc. Padrão', 'searchable' => false],
                        ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                    ];
                @endphp
                <x-datatable id="classPricingsTable" url="{{ route('finance.pricings.index') }}" :columns="$columns" />
            </div>
        </div>
    </div>
</x-app-layout>