<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Gestor de Blocos (Cabeçalhos/Rodapés)</h2>
                <p class="text-muted small mb-0">Crie os papéis timbrados que serão usados nos seus documentos em PDF.</p>
            </div>
            <a href="{{ route('admin.document-partials.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Novo Bloco
            </a>
        </div>

        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="partialsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nome do Bloco</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipo</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Data de Criação</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#partialsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.document-partials.index') }}',
                columns: [
                    {data: 'name', name: 'name', className: 'fw-bold text-dark'},
                    {data: 'type', name: 'type'},
                    {
                        data: 'created_at', 
                        name: 'created_at',
                        render: function(data) {
                            return new Date(data).toLocaleDateString('pt-BR');
                        }
                    },
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                }
            });
        });
    </script>
</x-app-layout>
