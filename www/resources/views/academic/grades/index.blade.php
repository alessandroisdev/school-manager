<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Séries e Anos Escolares</h2>
            <a href="{{ route('academic.grades.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nova Série
            </a>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'id', 'label' => 'ID', 'searchable' => false],
                    ['name' => 'name_badge', 'label' => 'Nome da Série'],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="gradesTable" url="{{ route('academic.grades.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
