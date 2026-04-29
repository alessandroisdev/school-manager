<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Gestão de Alunos</h2>
            <a href="{{ route('students.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Novo Aluno
            </a>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['data' => 'name', 'name' => 'name', 'label' => 'Nome do Aluno'],
                    ['data' => 'document', 'name' => 'document', 'label' => 'Documento'],
                    ['data' => 'contact', 'name' => 'contact', 'label' => 'Contato', 'orderable' => false, 'searchable' => false],
                    ['data' => 'status_badge', 'name' => 'status_badge', 'label' => 'Status', 'orderable' => false, 'searchable' => false],
                    ['data' => 'actions', 'name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="studentsTable" url="{{ route('students.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
