<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Captação (Leads)</h2>
                <p class="text-muted small mb-0">Gerencie intenções de matrícula vindas do portal público.</p>
            </div>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'lead_info', 'label' => 'Candidato a Aluno'],
                    ['name' => 'parent_info', 'label' => 'Responsável / Contato', 'searchable' => false],
                    ['name' => 'created_at', 'label' => 'Data da Solicitação', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="leadsTable" url="{{ route('secretariat.leads.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
