<x-app-layout>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Emissão em Lote</h2>
                <p class="text-muted small mb-0">Gerar contratos e recibos para a turma: <strong>{{ $class->name }}</strong></p>
            </div>
            <a href="{{ route('academic.classes.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <div class="glass-card p-4">
            <form action="{{ route('academic.classes.batch-generate', $class) }}" method="POST">
                @csrf
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">Selecione o Documento/Template *</label>
                        <select name="document_template_id" class="form-select bg-light border-0" required>
                            <option value="">-- Escolha um template --</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    Alunos Enturmados <span class="badge bg-primary rounded-pill fs-6">{{ $enrollments->count() }}</span>
                </h5>

                <div class="table-responsive mb-4">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll" checked>
                                    </div>
                                </th>
                                <th>Nome do Aluno</th>
                                <th>Matrícula</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="{{ $enrollment->student_id }}" checked>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-dark">{{ $enrollment->student->name }}</td>
                                    <td class="text-muted">{{ $enrollment->id }}</td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">Ativo</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Nenhum aluno ativo nesta turma.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-warning bg-warning bg-opacity-10 border-warning border-opacity-25 d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-3 text-warning me-3"></i>
                    <div>
                        <strong>Atenção:</strong> A geração em lote pode demorar alguns segundos. O sistema criará um histórico individual inalterável no perfil de cada aluno e fará o download de um arquivo <strong>PDF único</strong> contendo todos os documentos para facilitar a impressão.
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4" {{ $enrollments->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-file-earmark-pdf me-2"></i> Gerar Lote em PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.student-checkbox');

            if(selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => {
                        cb.checked = selectAll.checked;
                    });
                });
            }
        });
    </script>
</x-app-layout>
