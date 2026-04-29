<x-app-layout>
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <div class="glass-card p-5">
                    <i class="bi bi-tools text-primary mb-3" style="font-size: 4rem;"></i>
                    <h2 class="fw-bold text-dark">{{ $title }}</h2>
                    <p class="text-muted mt-3">Este módulo está em fase final de homologação. Em breve a interface de gerenciamento estará disponível aqui.</p>
                    <a href="{{ route('finance.invoices.index') }}" class="btn btn-outline-primary mt-4">
                        <i class="bi bi-arrow-left me-2"></i> Voltar ao Faturamento
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
