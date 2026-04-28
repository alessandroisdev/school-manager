<x-app-layout>
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        
        <div class="mb-6 flex items-center">
            <a href="{{ route('students.index') }}" class="text-gray-500 hover:text-blue-600 mr-3 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Editar Aluno</h2>
        </div>

        <div class="bg-white shadow-sm sm:rounded-xl border border-gray-100 overflow-hidden">
            <form action="{{ route('students.update', $student) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $student->name) }}" required 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Documento -->
                    <div>
                        <label for="document" class="block text-sm font-medium text-gray-700 mb-1">Documento (CPF/RG)</label>
                        <input type="text" name="document" id="document" value="{{ old('document', $student->document) }}" required 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2">
                        @error('document') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Data Nasc -->
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}" required 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2">
                        @error('birth_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end">
                    <a href="{{ route('students.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 mr-3 transition-colors shadow-sm">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm">
                        Atualizar Dados
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
