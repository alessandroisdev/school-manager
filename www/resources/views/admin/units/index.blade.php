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
                    ['data' => 'name', 'name' => 'name', 'label' => 'Nome da Unidade'],
                    ['data' => 'contact', 'name' => 'contact', 'label' => 'Contato', 'orderable' => false, 'searchable' => false],
                    ['data' => 'status_badge', 'name' => 'status_badge', 'label' => 'Status', 'orderable' => false, 'searchable' => false],
                    ['data' => 'actions', 'name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="unitsTable" url="{{ route('admin.units.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
