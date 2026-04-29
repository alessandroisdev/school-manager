<x-app-layout>
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Templates de Documentos</h2>
                <p class="text-muted small mb-0">Gerencie contratos, recibos e declarações oficiais gerados pelo
                    sistema.</p>
            </div>
            <a href="{{ route('admin.templates.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-magic me-2"></i> Novo Template
            </a>
        </div>

        <div class="glass-card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="templatesTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nome do
                                Documento</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipo</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Blocos
                                Associados</th>
                            <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#templatesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.templates.index') }}',
                columns: [
                    { data: 'name', name: 'name', className: 'fw-bold text-dark' },
                    { data: 'type', name: 'type' },
                    { data: 'partials', name: 'partials', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                }
            });
        });
    </script>
</x-app-layout>