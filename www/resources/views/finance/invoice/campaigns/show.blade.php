<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('finance.invoice.campaigns.index') }}"
                    class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left"></i> Voltar</a>
                <h2 class="h3 mb-0 text-dark fw-bold">Campanha: {{ $invoiceCampaign->name }}</h2>
            </div>

            <div class="d-flex gap-2">
                @if($stats['failed'] > 0)
                    <form action="{{ route('finance.invoice.campaigns.retry', $invoiceCampaign) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning shadow-sm fw-bold">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reprocessar {{ $stats['failed'] }} Falhas
                        </button>
                    </form>
                @endif

                @if($invoiceCampaign->status === 'completed' && $invoiceCampaign->zip_path)
                    <a href="{{ asset('storage/' . $invoiceCampaign->zip_path) }}" class="btn btn-success shadow-sm fw-bold"
                        target="_blank" download>
                        <i class="bi bi-file-zip me-1"></i> Baixar Lote (Gráfica)
                    </a>
                @endif
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="glass-card p-3 border-start border-4 border-primary text-center">
                    <h6 class="text-muted fw-bold mb-1">Total Gerado</h6>
                    <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-3 border-start border-4 border-success text-center">
                    <h6 class="text-muted fw-bold mb-1">E-mails Enviados</h6>
                    <h3 class="fw-bold text-success mb-0">{{ $stats['sent'] }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-3 border-start border-4 border-secondary text-center">
                    <h6 class="text-muted fw-bold mb-1">Na Fila (Processando)</h6>
                    <h3 class="fw-bold text-secondary mb-0">{{ $stats['pending'] }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="glass-card p-3 border-start border-4 border-danger text-center">
                    <h6 class="text-muted fw-bold mb-1">Falhas</h6>
                    <h3 class="fw-bold text-danger mb-0">{{ $stats['failed'] }}</h3>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h5 class="fw-bold mb-3">Logs de Disparo Individual</h5>
            @php
                $columns = [
                    ['name' => 'student_name', 'label' => 'Aluno'],
                    ['name' => 'email', 'label' => 'E-mail de Destino'],
                    ['name' => 'status_badge', 'label' => 'Status do Envio', 'searchable' => false],
                    ['name' => 'error_message', 'label' => 'Detalhes do Erro', 'searchable' => false],
                    ['name' => 'created_at', 'label' => 'Data', 'searchable' => false],
                ];
            @endphp

            <x-datatable id="itemsTable" url="{{ route('finance.invoice.campaigns.show', $invoiceCampaign) }}"
                :columns="$columns" />
        </div>
    </div>
</x-app-layout>