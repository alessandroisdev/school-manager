<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-0 text-dark fw-bold">Assinaturas Oficiais</h2>
                <p class="text-muted small mb-0">Gestores e diretores que assinam os documentos</p>
            </div>
            <div>
                <a href="{{ route('admin.official-documents.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm me-2">
                    <i class="bi bi-arrow-left me-1"></i> Voltar aos Ofícios
                </a>
                <a href="{{ route('admin.official-signers.create') }}" class="btn btn-primary fw-bold shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Novo Assinante
                </a>
            </div>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'info', 'label' => 'Identificação'],
                    ['name' => 'signature', 'label' => 'Rubrica (Imagem)', 'searchable' => false, 'orderable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="signersTable" url="{{ route('admin.official-signers.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
