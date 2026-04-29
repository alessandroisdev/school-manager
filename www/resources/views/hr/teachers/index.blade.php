<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Corpo Docente (Professores)</h2>
            <a href="{{ route('hr.teachers.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Novo Professor
            </a>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'name_avatar', 'label' => 'Professor e Especialidade'],
                    ['name' => 'workload', 'label' => 'Carga Max.', 'searchable' => false],
                    ['name' => 'status_badge', 'label' => 'Status', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="teachersTable" url="{{ route('hr.teachers.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
