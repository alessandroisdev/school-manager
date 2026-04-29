<x-app-layout>
    <div class="container-fluid">
        <div class="mb-4 d-flex align-items-center">
            <a href="{{ route('finance.class-pricings.index') }}" class="btn btn-outline-secondary me-3 shadow-sm"><i class="bi bi-arrow-left"></i> Voltar</a>
            <h2 class="h3 mb-0 text-dark fw-bold">Cadastrar Precificação</h2>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="glass-card p-4">
                    <form action="{{ route('finance.class-pricings.store') }}" method="POST">
                        @csrf
                        @include('finance.class-pricings._form')

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm"><i class="bi bi-check-lg me-1"></i> Salvar Regra</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="glass-card p-4 bg-primary bg-opacity-10 border-0 h-100">
                    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-2"></i> Dinâmica do Lote</h5>
                    <p class="text-muted small">A precificação é a regra que diz quanto custa um aluno estudar em uma determinada <strong>Série e Turno</strong>.</p>
                    <p class="text-muted small mb-0">Ao gerar faturas em lote ou emitir o Carnê de um aluno, o sistema procura a regra da série em que ele está enturmado, pega o <strong>Valor Anual</strong> e o divide automaticamente pela <strong>Quantidade de Parcelas</strong>.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
