<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Matérias e Disciplinas</h2>
            <a href="{{ route('academic.subjects.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nova Disciplina
            </a>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'name_avatar', 'label' => 'Nome da Disciplina'],
                    ['name' => 'workload', 'label' => 'Carga Horária Base', 'searchable' => false],
                    ['name' => 'status_badge', 'label' => 'Status', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="subjectsTable" url="{{ route('academic.subjects.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
