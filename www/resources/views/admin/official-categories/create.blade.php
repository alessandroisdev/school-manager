<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Nova Categoria</h2>
                <p class="text-muted small mb-0">Cadastrar tipo de comunicação oficial</p>
            </div>
            <a href="{{ route('admin.official-categories.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4" style="max-width: 600px;">
            <form action="{{ route('admin.official-categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Nome da Categoria *</label>
                    <input type="text" name="name" class="form-control bg-light border-0" required placeholder="Ex: Portaria, Ofício, Memorando">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small">Sigla (Usada na numeração) *</label>
                    <input type="text" name="acronym" class="form-control bg-light border-0" required placeholder="Ex: PORT, OF">
                    <small class="text-muted">A numeração sairá como 001/2026/SIGLA</small>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-save me-2"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
