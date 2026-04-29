<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-0 text-dark fw-bold">Categorias de Ofícios</h2>
                <p class="text-muted small mb-0">Gerencie Portarias, Memorandos, Ofícios, etc.</p>
            </div>
            <div>
                <a href="{{ route('admin.official-documents.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm me-2">
                    <i class="bi bi-arrow-left me-1"></i> Voltar aos Ofícios
                </a>
                <a href="{{ route('admin.official-categories.create') }}" class="btn btn-primary fw-bold shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Nova Categoria
                </a>
            </div>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'name_badge', 'label' => 'Nome da Categoria'],
                    ['name' => 'acronym_badge', 'label' => 'Sigla (Acronimo)'],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="categoriesTable" url="{{ route('admin.official-categories.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
