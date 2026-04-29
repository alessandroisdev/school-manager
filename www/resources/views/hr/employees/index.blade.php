<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Colaboradores / RH</h2>
            <a href="{{ route('hr.employees.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Novo Colaborador
            </a>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['data' => 'name', 'name' => 'name', 'label' => 'Nome do Colaborador'],
                    ['data' => 'document', 'name' => 'document', 'label' => 'Documento'],
                    ['data' => 'hire_date_formatted', 'name' => 'hire_date_formatted', 'label' => 'Contratação', 'searchable' => false],
                    ['data' => 'status_badge', 'name' => 'status_badge', 'label' => 'Status', 'searchable' => false],
                    ['data' => 'actions', 'name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="employeesTable" url="{{ route('hr.employees.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
