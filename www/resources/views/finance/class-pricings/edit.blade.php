<x-app-layout>
    <div class="container-fluid">
        <div class="mb-4 d-flex align-items-center">
            <a href="{{ route('finance.class-pricings.index') }}" class="btn btn-outline-secondary me-3 shadow-sm"><i class="bi bi-arrow-left"></i> Voltar</a>
            <h2 class="h3 mb-0 text-dark fw-bold">Editar Precificação</h2>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="glass-card p-4">
                    <form action="{{ route('finance.class-pricings.update', $classPricing) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('finance.class-pricings._form', ['classPricing' => $classPricing])

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm"><i class="bi bi-check-lg me-1"></i> Atualizar Regra</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
