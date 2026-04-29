<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Gestão de Unidades (Franquias)</h2>
                <p class="text-muted small mb-0">Cadastre escolas, filiais e gerencie polos.</p>
            </div>
            <a href="{{ route('admin.units.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nova Unidade
            </a>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'unit_info', 'label' => 'Nome da Unidade'],
                    ['name' => 'contact', 'label' => 'Contato', 'searchable' => false],
                    ['name' => 'status_badge', 'label' => 'Status', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="unitsTable" url="{{ route('admin.units.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
