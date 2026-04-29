<x-app-layout>
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Editar Bloco</h2>
                <p class="text-muted small mb-0">{{ $documentPartial->name }}</p>
            </div>
            <a href="{{ route('admin.partials.index') }}" class="btn btn-light fw-bold text-secondary">
                <i class="bi bi-arrow-left me-2"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4 mx-auto" style="max-width: 900px;">
            <form action="{{ route('admin.partials.update', $documentPartial) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">Nome Identificador *</label>
                        <input type="text" name="name" class="form-control bg-light border-0"
                            value="{{ $documentPartial->name }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">Tipo de Bloco *</label>
                        <select name="type" class="form-select bg-light border-0" required>
                            <option value="header" {{ $documentPartial->type === 'header' ? 'selected' : '' }}>Cabeçalho
                                (Topo)</option>
                            <option value="footer" {{ $documentPartial->type === 'footer' ? 'selected' : '' }}>Rodapé
                                (Base)</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="isActive" name="is_active"
                                value="1" {{ $documentPartial->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-muted small" for="isActive">Ativo</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold text-muted small">Conteúdo do Bloco (HTML) *</label>
                        <textarea name="content" id="html-editor"
                            class="form-control">{{ $documentPartial->content }}</textarea>
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
    <!-- TinyMCE Open Source (Sem aviso de API Key) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            tinymce.init({
                selector: '#html-editor',
                height: 400,
                menubar: 'file edit view insert format tools table',
                plugins: 'lists link image table code help wordcount',
                toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | table image | code',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                language: 'pt_BR',
                promotion: false, // Esconde botão de upgrade
                branding: false, // Esconde "Powered by TinyMCE"
                convert_urls: false, // <-- Fix the image relative URL issue

                // Configuração de Upload de Imagens
                images_upload_handler: function (blobInfo, progress) {
                    return new Promise((resolve, reject) => {
                        const formData = new FormData();
                        formData.append('image', blobInfo.blob(), blobInfo.filename());
                        formData.append('_token', '{{ csrf_token() }}');

                        fetch('{{ route('admin.upload.image') }}', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.url) {
                                    resolve(data.url);
                                } else {
                                    reject('Erro no upload.');
                                }
                            })
                            .catch(err => {
                                reject('Falha de rede.');
                            });
                    });
                }
            });
        });
    </script>
</x-app-layout>