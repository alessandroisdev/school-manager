<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Diário de Notas</h2>
                <p class="text-muted small mb-0">{{ $assignment->schoolClass->name }} - {{ $assignment->subject->name }}</p>
            </div>
            <a href="{{ route('academic.diary.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar aos Diários
            </a>
        </div>

        <div class="row g-4">
            <!-- Formulário de Notas -->
            <div class="col-lg-8">
                <div class="glass-card p-4">
                    <form action="{{ route('academic.diary.evaluations.store', $assignment->id) }}" method="POST">
                        @csrf
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-bold text-muted small">Nome da Avaliação *</label>
                                <input type="text" name="name" id="name" class="form-control bg-light border-0" placeholder="Ex: Prova Bimestral" required>
                            </div>
                            <div class="col-md-6">
                                <label for="date" class="form-label fw-bold text-muted small">Data da Aplicação *</label>
                                <input type="date" name="date" id="date" class="form-control bg-light border-0" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="max_score" class="form-label fw-bold text-muted small">Nota Máxima *</label>
                                <input type="number" step="0.1" name="max_score" id="max_score" class="form-control bg-light border-0" value="10.0" required>
                            </div>
                            <div class="col-md-4">
                                <label for="weight" class="form-label fw-bold text-muted small">Peso *</label>
                                <input type="number" step="0.1" name="weight" id="weight" class="form-control bg-light border-0" value="1.0" required>
                            </div>
                            <div class="col-md-4 d-none">
                                <input type="hidden" name="evaluation_type_id" value="{{ $defaultType->id }}">
                            </div>
                        </div>

                        <h6 class="fw-bold text-info mb-3"><i class="bi bi-award me-2"></i> Lançamento de Notas</h6>
                        <p class="small text-muted mb-3">Deixe em branco caso o aluno não tenha feito a prova.</p>
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Aluno</th>
                                        <th width="150">Nota Final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($enrollments as $enrollment)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $enrollment->student->name }}</div>
                                            </td>
                                            <td>
                                                <input type="number" step="0.1" min="0" name="grades[{{ $enrollment->student_id }}]" class="form-control form-control-sm text-center fw-bold" placeholder="0.0">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-4">Nenhum aluno matriculado nesta turma.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($enrollments->count() > 0)
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary fw-bold px-4">
                                    <i class="bi bi-check-lg me-2"></i> Salvar Avaliação e Notas
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Histórico Recente -->
            <div class="col-lg-4">
                <div class="glass-card p-4 h-100">
                    <h6 class="fw-bold text-muted text-uppercase mb-4" style="font-size: 0.75rem; letter-spacing: 1px;">Últimas Avaliações</h6>
                    
                    <div class="list-group list-group-flush">
                        @forelse($evaluations->take(5) as $evaluation)
                            <div class="list-group-item px-0 bg-transparent py-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark">{{ $evaluation->name }}</span>
                                    <span class="badge bg-light text-dark">Máx: {{ $evaluation->max_score }}</span>
                                </div>
                                <div class="small text-muted">{{ \Carbon\Carbon::parse($evaluation->date)->format('d/m/Y') }}</div>
                                <div class="small text-muted mt-1"><i class="bi bi-people"></i> {{ $evaluation->gradeEntries()->count() }} notas lançadas</div>
                            </div>
                        @empty
                            <div class="text-muted small text-center py-4">Nenhuma avaliação registrada.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
