<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Diários de Classe</h2>
                <p class="text-muted small mb-0">Selecione uma turma para lançar Frequência ou Notas.</p>
            </div>
        </div>

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'diary_name', 'label' => 'Turma e Disciplina'],
                    ['name' => 'teacher_name', 'label' => 'Professor Responsável', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Acesso ao Diário', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <div class="glass-card p-4">
                <x-datatable id="diariesTable" url="{{ route('academic.diary.index') }}" :columns="$columns" />
            </div>
        </div>
    </div>
</x-app-layout>
