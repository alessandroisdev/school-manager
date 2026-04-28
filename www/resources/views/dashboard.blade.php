<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if (session('status'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl">
            <div class="p-8 text-gray-900 flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold tracking-tight">Bem-vindo(a) de volta, {{ Auth::user()->name }}!</h3>
                    <p class="mt-1 text-gray-500">
                        O painel de controle do SGE está pronto para uso.
                    </p>
                </div>
                
                <div class="text-right">
                    <p class="text-sm text-gray-500">Unidade Operacional Ativa</p>
                    <p class="text-lg font-black text-blue-600">
                        {{ Auth::user()->units->where('id', session('active_unit_id'))->first()?->name ?? 'Nenhuma unidade configurada' }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Alunos Ativos</p>
                    <p class="text-2xl font-bold text-gray-800">1,204</p>
                </div>
            </div>
            
            <!-- Card 2 -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Frequência Hoje</p>
                    <p class="text-2xl font-bold text-gray-800">96.5%</p>
                </div>
            </div>
            
            <!-- Card 3 -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Bimestre Ativo</p>
                    <p class="text-2xl font-bold text-gray-800">2º Bim</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
