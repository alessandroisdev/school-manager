<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Diário de Frequência</h2>
                <p class="text-muted small mb-0">{{ $assignment->schoolClass->name }} - {{ $assignment->subject->name }}</p>
            </div>
            <a href="{{ route('academic.diary.index') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar aos Diários
            </a>
        </div>

        <div class="row g-4">
            <!-- Formulário de Chamada -->
            <div class="col-lg-8">
                <div class="glass-card p-4">
                    <form action="{{ route('academic.diary.lessons.store', $assignment->id) }}" method="POST">
                        @csrf
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="date" class="form-label fw-bold text-muted small">Data da Aula *</label>
                                <input type="date" name="date" id="date" class="form-control bg-light border-0" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-8">
                                <label for="notes" class="form-label fw-bold text-muted small">Conteúdo Lecionado</label>
                                <input type="text" name="notes" id="notes" class="form-control bg-light border-0" placeholder="Ex: Equações de 2º Grau" required>
                            </div>
                        </div>

                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-list-check me-2"></i> Lista de Chamada</h6>
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Aluno</th>
                                        <th class="text-center" width="100">Presente</th>
                                        <th class="text-center" width="100">Falta</th>
                                        <th class="text-center" width="100">Justificado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($enrollments as $enrollment)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $enrollment->student->name }}</div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline d-flex justify-content-center m-0">
                                                    <input class="form-check-input" type="radio" name="attendance[{{ $enrollment->student_id }}]" value="presente" checked>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline d-flex justify-content-center m-0">
                                                    <input class="form-check-input border-danger" type="radio" name="attendance[{{ $enrollment->student_id }}]" value="falta">
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline d-flex justify-content-center m-0">
                                                    <input class="form-check-input border-warning" type="radio" name="attendance[{{ $enrollment->student_id }}]" value="justificado">
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Nenhum aluno matriculado nesta turma.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($enrollments->count() > 0)
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary fw-bold px-4">
                                    <i class="bi bi-check-lg me-2"></i> Salvar Frequência
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Histórico Recente -->
            <div class="col-lg-4">
                <div class="glass-card p-4 h-100">
                    <h6 class="fw-bold text-muted text-uppercase mb-4" style="font-size: 0.75rem; letter-spacing: 1px;">Últimas Aulas Lançadas</h6>
                    
                    <div class="list-group list-group-flush">
                        @forelse($lessons->take(5) as $lesson)
                            <div class="list-group-item px-0 bg-transparent py-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($lesson->date)->format('d/m/Y') }}</span>
                                    <span class="badge bg-light text-dark">{{ $lesson->attendanceRecords()->where('status', 'presente')->count() }} Presenças</span>
                                </div>
                                <div class="small text-muted text-truncate">{{ $lesson->notes }}</div>
                            </div>
                        @empty
                            <div class="text-muted small text-center py-4">Nenhuma aula registrada.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
