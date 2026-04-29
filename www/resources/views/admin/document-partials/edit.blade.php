<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Editar Bloco</h2>
                <p class="text-muted small mb-0">{{ $documentPartial->name }}</p>
            </div>
            <a href="{{ route('admin.document-partials.index') }}" class="btn btn-light fw-bold text-secondary">
                <i class="bi bi-arrow-left me-2"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4 mx-auto" style="max-width: 900px;">
            <form action="{{ route('admin.document-partials.update', $documentPartial) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold text-muted small">Nome Identificador *</label>
                        <input type="text" name="name" class="form-control bg-light border-0" value="{{ $documentPartial->name }}" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">Tipo de Bloco *</label>
                        <select name="type" class="form-select bg-light border-0" required>
                            <option value="header" {{ $documentPartial->type === 'header' ? 'selected' : '' }}>Cabeçalho (Topo)</option>
                            <option value="footer" {{ $documentPartial->type === 'footer' ? 'selected' : '' }}>Rodapé (Base)</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold text-muted small">Conteúdo do Bloco (HTML) *</label>
                        <textarea name="content" id="html-editor" class="form-control" rows="10">{{ $documentPartial->content }}</textarea>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-save me-2"></i> Atualizar Bloco
                    </button>
                </div>
            </form>
        </div>
    </div>

    @stack('scripts')
    <!-- TinyMCE CDN -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            tinymce.init({
                selector: '#html-editor',
                height: 300,
                menubar: false,
                plugins: 'lists link image table code help wordcount',
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | table image | code | removeformat',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                language: 'pt_BR'
            });
        });
    </script>
</x-app-layout>
