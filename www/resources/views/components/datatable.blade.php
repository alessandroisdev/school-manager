@props(['id', 'url', 'columns'])

<div class="glass-card table-responsive w-100">
    <table id="{{ $id }}" class="table table-hover table-borderless align-middle w-100">
        <thead class="table-light text-muted text-uppercase small">
            <tr>
                @foreach($columns as $col)
                    <th scope="col" class="py-3 px-4">{{ $col['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="border-top">
            <!-- DataTables injeta os dados via AJAX aqui -->
        </tbody>
    </table>
</div>

@push('scripts')
<script type="module">
    document.addEventListener('DOMContentLoaded', function () {
        let cols = @json($columns);
        let dtCols = cols.map(c => {
            return {
                data: c.name,
                name: c.name,
                searchable: c.searchable !== false,
                orderable: c.orderable !== false,
                className: 'py-3 px-4'
            };
        });

        $('#{{ $id }}').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ $url }}',
            columns: dtCols,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
            },
            dom: '<"d-flex justify-content-between align-items-center mb-4"<"col-md-6"l><"col-md-6 text-end"f>>rt<"d-flex justify-content-between align-items-center mt-4"<"col-md-6"i><"col-md-6 text-end"p>>',
            drawCallback: function() {
                // Bootstrap tweaks post-draw
                $('.dataTables_paginate .paginate_button').addClass('btn btn-sm btn-outline-secondary ms-1');
                $('.dataTables_paginate .paginate_button.current').addClass('active').removeClass('btn-outline-secondary').addClass('btn-primary');
                $('.dataTables_filter input').addClass('form-control form-control-sm d-inline-block w-auto ms-2 border-0 bg-light');
                $('.dataTables_length select').addClass('form-select form-select-sm d-inline-block w-auto mx-2 border-0 bg-light');
            }
        });
    });
</script>
@endpush
