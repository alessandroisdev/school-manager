<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-0 text-dark fw-bold">Comunicação Oficial</h2>
                <p class="text-muted small mb-0">Gestão de Ofícios, Portarias e Memorandos</p>
            </div>
            <div>
                <!-- Links auxiliares caso as rotas não tenham sidebar dedicada -->
                <a href="{{ route('admin.official-categories.index') }}"
                    class="btn btn-outline-secondary fw-bold shadow-sm me-2">Categorias</a>
                <a href="{{ route('admin.official-signers.index') }}"
                    class="btn btn-outline-secondary fw-bold shadow-sm me-2">Assinaturas</a>

                <a href="{{ route('admin.documents.create') }}" class="btn btn-primary fw-bold shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Redigir Ofício
                </a>
            </div>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'doc_number', 'label' => 'Expediente / Data'],
                    ['name' => 'subject_info', 'label' => 'Assunto / Destinatário'],
                    ['name' => 'status_badge', 'label' => 'Status', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp

            <x-datatable id="officialDocsTable" url="{{ route('admin.documents.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>