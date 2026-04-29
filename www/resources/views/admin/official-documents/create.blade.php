<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Novo Ofício / Documento</h2>
                <p class="text-muted small mb-0">Redação oficial (Padrão ABNT / PR)</p>
            </div>
            <a href="{{ route('admin.documents.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4">
            <form action="{{ route('admin.documents.store') }}" method="POST">
                @csrf
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">Categoria *</label>
                        <select name="category_id" class="form-select bg-light border-0" required>
                            <option value="">Selecione...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->acronym }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">Data do Expediente *</label>
                        <input type="date" name="date" class="form-control bg-light border-0"
                            value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">Destinatário (Vocativo / Cargo)</label>
                        <input type="text" name="recipient" class="form-control bg-light border-0"
                            placeholder="Ex: Excelentíssimo Senhor Prefeito Municipal...">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-bold text-muted small">Assunto *</label>
                        <input type="text" name="subject" class="form-control bg-light border-0" required
                            placeholder="Resumo claro do tema">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">Assinatura</label>
                        <select name="signer_id" class="form-select bg-light border-0">
                            <option value="">Nenhum (Assinatura manual)</option>
                            @foreach($signers as $signer)
                                <option value="{{ $signer->id }}">{{ $signer->name }} - {{ $signer->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small">Corpo do Documento *</label>
                    <!-- Wrapper que simula uma folha A4 com as margens reais da ABNT -->
                    <div style="background: #e9ecef; padding: 20px; border-radius: 8px;">
                        <textarea name="content" id="abnt-editor" class="form-control"></textarea>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-save me-2"></i> Salvar Rascunho
                    </button>
                </div>
            </form>
        </div>
    </div>

    @stack('scripts')
    <!-- TinyMCE Open Source -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            tinymce.init({
                selector: '#abnt-editor',
                height: 800, // Altura que simula uma A4 em monitores comuns
                menubar: 'file edit view insert format tools table',
                plugins: 'lists link image table pagebreak wordcount',
                toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table image | pagebreak',

                // INJEÇÃO ABNT NO EDITOR
                content_style: `
                    @import url('https://fonts.googleapis.com/css2?family=Carlito&display=swap');
                    body {
                        font-family: 'Carlito', Arial, sans-serif;
                        font-size: 12pt;
                        line-height: 1.5;
                        text-align: justify;
                        /* Simulação das margens A4: Topo 3cm, Esq 3cm, Dir 1.5cm, Fundo 2cm */
                        /* Em pixels (approx): 3cm = ~113px, 1.5cm = ~57px, 2cm = ~75px */
                        padding: 113px 57px 75px 113px;
                        margin: 0 auto;
                        max-width: 210mm; /* Largura A4 */
                        background-color: white;
                        box-shadow: 0 0 5px rgba(0,0,0,0.1);
                    }
                    p {
                        text-indent: 2.5cm; /* Recuo de parágrafo */
                        margin-top: 0;
                        margin-bottom: 12pt;
                    }
                `,
                language: 'pt_BR',
                promotion: false,
                branding: false,
                convert_urls: false,

                images_upload_handler: function (blobInfo, progress) {
                    return new Promise((resolve, reject) => {
                        let xhr = new XMLHttpRequest();
                        xhr.open('POST', '{{ route('admin.upload.image') }}');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                        xhr.upload.onprogress = function (e) {
                            progress(e.loaded / e.total * 100);
                        };

                        xhr.onload = function () {
                            if (xhr.status === 403) {
                                reject({ message: 'Erro HTTP: ' + xhr.status, remove: true });
                                return;
                            }
                            if (xhr.status < 200 || xhr.status >= 300) {
                                reject('Erro no upload: ' + xhr.status);
                                return;
                            }
                            let json = JSON.parse(xhr.responseText);
                            if (!json || typeof json.location != 'string') {
                                reject('JSON inválido: ' + xhr.responseText);
                                return;
                            }
                            resolve(json.location);
                        };

                        xhr.onerror = function () {
                            reject('Falha na rede ou erro de CORS durante o upload.');
                        };

                        let formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        xhr.send(formData);
                    });
                }
            });
        });
    </script>
</x-app-layout>