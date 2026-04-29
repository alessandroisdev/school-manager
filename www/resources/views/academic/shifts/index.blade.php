<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-dark fw-bold">Gestão de Turnos</h2>
            <a href="{{ route('academic.shifts.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Novo Turno
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'name_badge', 'label' => 'Nome do Turno'],
                    ['name' => 'time_range', 'label' => 'Horário de Aula', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="shiftsTable" url="{{ route('academic.shifts.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
