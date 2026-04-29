<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Editar Assinante</h2>
                <p class="text-muted small mb-0">Atualizar autoridade para assinatura</p>
            </div>
            <a href="{{ route('admin.official-signers.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4" style="max-width: 600px;">
            <form action="{{ route('admin.official-signers.update', $officialSigner) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Nome Completo *</label>
                    <input type="text" name="name" class="form-control bg-light border-0" required value="{{ $officialSigner->name }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Cargo / Título *</label>
                    <input type="text" name="title" class="form-control bg-light border-0" required value="{{ $officialSigner->title }}">
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">Imagem da Assinatura Atual</label>
                    @if($officialSigner->signature_image_path)
                        <div class="mb-2 p-2 bg-light border rounded text-center">
                            <img src="{{ Storage::url($officialSigner->signature_image_path) }}" height="60" alt="Assinatura">
                        </div>
                        <div class="form-check text-danger mb-2">
                            <input class="form-check-input border-danger" type="checkbox" name="remove_signature" id="removeSignature" value="1">
                            <label class="form-check-label small fw-bold" for="removeSignature">Remover assinatura atual (deixar física)</label>
                        </div>
                    @else
                        <div class="small text-muted mb-2">Nenhuma imagem cadastrada (Assinatura física configurada).</div>
                    @endif
                    <label class="form-label fw-bold text-muted small mt-2">Nova Imagem (Substitui a atual, se houver)</label>
                    <input type="file" name="signature_file" class="form-control bg-light border-0" accept=".png,.jpg,.jpeg">
                </div>

                <div class="mb-4 form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ $officialSigner->is_active ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-muted small" for="isActive">Assinante Ativo</label>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-save me-2"></i> Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
