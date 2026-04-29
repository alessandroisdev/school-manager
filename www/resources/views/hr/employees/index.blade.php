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
                    ['name' => 'name_avatar', 'label' => 'Nome do Colaborador'],
                    ['name' => 'document', 'label' => 'Documento'],
                    ['name' => 'hire_date_formatted', 'label' => 'Contratação', 'searchable' => false],
                    ['name' => 'status_badge', 'label' => 'Status', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="employeesTable" url="{{ route('hr.employees.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
