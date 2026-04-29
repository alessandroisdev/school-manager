<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Novo Assinante</h2>
                <p class="text-muted small mb-0">Cadastrar autoridade para assinar ofícios</p>
            </div>
            <a href="{{ route('admin.official-signers.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4" style="max-width: 600px;">
            <form action="{{ route('admin.official-signers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Nome Completo *</label>
                    <input type="text" name="name" class="form-control bg-light border-0" required placeholder="Ex: Dr. Fulano de Tal">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Cargo / Título *</label>
                    <input type="text" name="title" class="form-control bg-light border-0" required placeholder="Ex: Diretor Geral">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Imagem da Assinatura (Fundo Transparente)</label>
                    <input type="file" name="signature_file" class="form-control bg-light border-0" accept=".png,.jpg,.jpeg">
                    <small class="text-muted">Deixe em branco para assinatura física (à caneta).</small>
                </div>
                <div class="mb-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                    <label class="form-check-label fw-bold text-muted small" for="isActive">Assinante Ativo</label>
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
