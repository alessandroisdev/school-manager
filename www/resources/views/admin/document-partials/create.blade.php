<x-app-layout>
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Novo Bloco de Documento</h2>
                <p class="text-muted small mb-0">Crie um cabeçalho ou rodapé reutilizável.</p>
            </div>
            <a href="{{ route('admin.partials.index') }}" class="btn btn-light fw-bold text-secondary">
                <i class="bi bi-arrow-left me-2"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4 mx-auto" style="max-width: 900px;">
            <form action="{{ route('admin.partials.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">Nome Identificador *</label>
                        <input type="text" name="name" class="form-control bg-light border-0" required
                            placeholder="Ex: Cabeçalho Padrão com CNPJ">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">Tipo de Bloco *</label>
                        <select name="type" class="form-select bg-light border-0" required>
                            <option value="header">Cabeçalho (Topo)</option>
                            <option value="footer">Rodapé (Base)</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="isActive" name="is_active"
                                value="1" checked>
                            <label class="form-check-label fw-bold text-muted small" for="isActive">Ativo</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold text-muted small">Conteúdo do Bloco (HTML) *</label>
                        <textarea name="content" id="html-editor" class="form-control"></textarea>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-save me-2"></i> Salvar Bloco
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
                promotion: false,
                branding: false,
                convert_urls: false, // <-- Impede o TinyMCE de transformar /storage em ../../storage

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