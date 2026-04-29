<x-app-layout>
    <div class="container-fluid">
        <div class="mb-4 d-flex align-items-center">
            <a href="{{ route('finance.bank-accounts.index') }}" class="btn btn-outline-secondary me-3 shadow-sm"><i class="bi bi-arrow-left"></i> Voltar</a>
            <h2 class="h3 mb-0 text-dark fw-bold">Cadastrar Conta Bancária</h2>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="glass-card p-4">
                    <form action="{{ route('finance.bank-accounts.store') }}" method="POST">
                        @csrf
                        @include('finance.bank-accounts._form')

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm"><i class="bi bi-check-lg me-1"></i> Salvar Conta</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="glass-card p-4 bg-primary bg-opacity-10 border-0 h-100">
                    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-2"></i> Como funciona?</h5>
                    <p class="text-muted small">As contas bancárias cadastradas aqui são utilizadas pelo <strong>OpenBoleto</strong> para gerar os boletos e carnês dos seus alunos.</p>
                    <p class="text-muted small mb-0">Preencha corretamente o código do banco, a agência e a conta. As instruções de multa e juros também podem ser personalizadas.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
