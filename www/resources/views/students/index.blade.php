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
                    ['name' => 'name_avatar', 'label' => 'Nome do Aluno'],
                    ['name' => 'document', 'label' => 'Documento'],
                    ['name' => 'contact', 'label' => 'Contato', 'orderable' => false],
                    ['name' => 'status_badge', 'label' => 'Status', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="studentsTable" url="{{ route('students.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
