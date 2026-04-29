<x-app-layout>
    <div class="container-fluid">
        @php
            $isEdit = isset($documentTemplate);
            $actionUrl = $isEdit ? route('admin.templates.update', $documentTemplate) : route('admin.templates.store');
        @endphp

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold"><i class="bi bi-magic text-primary"></i> Builder de Documento</h2>
                <p class="text-muted small mb-0">
                    {{ $isEdit ? 'Editando: ' . $documentTemplate->name : 'Criando novo modelo' }}</p>
            </div>
            <a href="{{ route('admin.templates.index') }}" class="btn btn-light fw-bold text-secondary">
                <i class="bi bi-arrow-left me-2"></i> Voltar
            </a>
        </div>

        <form action="{{ $actionUrl }}" method="POST">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <div class="row g-4">
                <!-- Coluna Esquerda: O Editor e Configurações -->
                <div class="col-lg-9">
                    <div class="glass-card p-4 mb-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-muted small">Nome do Template *</label>
                                <input type="text" name="name" class="form-control bg-light border-0"
                                    value="{{ $documentTemplate->name ?? '' }}" required
                                    placeholder="Ex: Contrato Padrão Ensino Médio 2026">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted small">Categoria *</label>
                                <select name="type" class="form-select bg-light border-0" required>
                                    <option value="contract" {{ ($documentTemplate->type ?? '') == 'contract' ? 'selected' : '' }}>Contrato / Termo</option>
                                    <option value="certificate" {{ ($documentTemplate->type ?? '') == 'certificate' ? 'selected' : '' }}>Certificado</option>
                                    <option value="receipt" {{ ($documentTemplate->type ?? '') == 'receipt' ? 'selected' : '' }}>Recibo</option>
                                    <option value="statement" {{ ($documentTemplate->type ?? '') == 'statement' ? 'selected' : '' }}>Declaração</option>
                                    <option value="other" {{ ($documentTemplate->type ?? '') == 'other' ? 'selected' : '' }}>Outro</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Cabeçalho (Papel Timbrado
                                    Topo)</label>
                                <select name="header_id" class="form-select bg-light border-0">
                                    <option value="">-- Nenhum --</option>
                                    @foreach($headers as $header)
                                        <option value="{{ $header->id }}" {{ ($documentTemplate->header_id ?? '') == $header->id ? 'selected' : '' }}>{{ $header->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Rodapé (Papel Timbrado Base)</label>
                                <select name="footer_id" class="form-select bg-light border-0">
                                    <option value="">-- Nenhum --</option>
                                    @foreach($footers as $footer)
                                        <option value="{{ $footer->id }}" {{ ($documentTemplate->footer_id ?? '') == $footer->id ? 'selected' : '' }}>{{ $footer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Corpo do Documento (Rich Text) *</label>
                            <textarea name="content" id="document-editor"
                                class="form-control">{{ $documentTemplate->content ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Coluna Direita: Variáveis Mágicas (Shortcodes) -->
                <div class="col-lg-3">
                    <div class="glass-card p-4 sticky-top" style="top: 100px;">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-lightning-charge-fill me-1"></i> Variáveis
                            Dinâmicas</h6>
                        <p class="small text-muted mb-3">Clique em uma tag para inseri-la diretamente no editor.</p>

                        <div class="mb-3">
                            <span class="d-block text-uppercase fw-bold text-secondary small mb-2">Dados do Aluno</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2 text-start tag-btn"
                                data-tag="[ALUNO_NOME]">[ALUNO_NOME]</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2 text-start tag-btn"
                                data-tag="[ALUNO_CPF]">[ALUNO_CPF]</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2 text-start tag-btn"
                                data-tag="[ALUNO_MATRICULA]">[ALUNO_MATRICULA]</button>
                        </div>

                        <div class="mb-3">
                            <span class="d-block text-uppercase fw-bold text-secondary small mb-2">Dados do
                                Responsável</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2 text-start tag-btn"
                                data-tag="[RESPONSAVEL_NOME]">[RESPONSAVEL_NOME]</button>
                        </div>

                        <div class="mb-3">
                            <span class="d-block text-uppercase fw-bold text-secondary small mb-2">Dados da Escola /
                                Turma</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2 text-start tag-btn"
                                data-tag="[UNIDADE_NOME]">[UNIDADE_NOME]</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2 text-start tag-btn"
                                data-tag="[UNIDADE_CNPJ]">[UNIDADE_CNPJ]</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2 text-start tag-btn"
                                data-tag="[CURSO_NOME]">[CURSO_NOME]</button>
                        </div>

                        <div class="mb-4">
                            <span class="d-block text-uppercase fw-bold text-secondary small mb-2">Sistema</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2 text-start tag-btn"
                                data-tag="[DATA_ATUAL]">[DATA_ATUAL]</button>
                        </div>

                        <div class="d-grid mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary fw-bold shadow-sm">
                                <i class="bi bi-save me-2"></i> Salvar Modelo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @stack('scripts')
    <!-- TinyMCE Open Source (Sem aviso de API Key) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            tinymce.init({
                selector: '#document-editor',
                height: 600,
                menubar: 'file edit view insert format tools table',
                plugins: 'lists link image table code help wordcount pagebreak nonbreaking advlist',
                toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table image | pagebreak code',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                language: 'pt_BR',
                pagebreak_separator: "<!-- pagebreak -->",
                promotion: false, // Esconde botão de upgrade
                branding: false, // Esconde "Powered by TinyMCE"
                convert_urls: false, // <-- Evita que imagens fiquem com caminhos relativos quebrados (../../storage)

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

            // Lógica para clicar no botão da tag e inserir no editor
            document.querySelectorAll('.tag-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    const tag = this.getAttribute('data-tag');

                    // Insere no editor
                    if (tinymce.activeEditor) {
                        tinymce.activeEditor.execCommand('mceInsertContent', false, tag);
                    }

                    // Feedback visual
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check2"></i> Inserido!';
                    this.classList.replace('btn-outline-secondary', 'btn-success');
                    this.classList.add('text-white');

                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.classList.replace('btn-success', 'btn-outline-secondary');
                        this.classList.remove('text-white');
                    }, 1000);
                });
            });
        });
    </script>
</x-app-layout>