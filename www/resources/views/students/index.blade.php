<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Alunos Matriculados</h2>
            <a href="{{ route('students.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm">
                + Nova Matrícula
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mb-4">
            @php
                $columns = [
                    ['name' => 'name_avatar', 'label' => 'Nome do Aluno'],
                    ['name' => 'document', 'label' => 'Documento'],
                    ['name' => 'birth_date_formatted', 'label' => 'Data Nasc.', 'searchable' => false],
                    ['name' => 'actions', 'label' => 'Ações', 'orderable' => false, 'searchable' => false]
                ];
            @endphp
            
            <x-datatable id="studentsTable" url="{{ route('students.index') }}" :columns="$columns" />
        </div>
    </div>
</x-app-layout>
