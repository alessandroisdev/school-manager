<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Gestão de Matrículas</h2>
            <a href="{{ route('enrollments.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Enturmar Aluno
            </a>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'student_name', 'label' => 'Aluno Matriculado'],
                    ['name' => 'class_info', 'label' => 'Turma Alocada', 'searchable' => false],
                    ['name' => 'status_badge', 'label' => 'Status', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="enrollmentsTable" url="{{ route('enrollments.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
