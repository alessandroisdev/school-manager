<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Alocação de Professores</h2>
            <a href="{{ route('academic.assignments.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nova Alocação
            </a>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'teacher_name', 'label' => 'Professor'],
                    ['name' => 'class_info', 'label' => 'Turma', 'searchable' => false],
                    ['name' => 'subject_info', 'label' => 'Disciplina', 'searchable' => false],
                    ['name' => 'workload', 'label' => 'Carga H.', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="assignmentsTable" url="{{ route('academic.assignments.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
