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
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="notes" class="form-label fw-bold text-muted small m-0">Conteúdo Lecionado / Plano de Aula</label>
                                    <button type="button" class="btn btn-sm btn-light text-primary fw-bold px-3 py-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#aiLessonModal" style="border: 1px solid #cce5ff;">
                                        <i class="bi bi-stars text-warning"></i> Gerar com IA
                                    </button>
                                </div>
                                <textarea name="notes" id="notes" class="form-control bg-light border-0" rows="2" placeholder="Ex: Equações de 2º Grau" required></textarea>
                            </div>
                        </div>

                        <!-- Modal AI Lesson Planner -->
                        <div class="modal fade" id="aiLessonModal" tabindex="-1" aria-labelledby="aiLessonModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-primary bg-opacity-10 border-0">
                                        <h5 class="modal-title text-primary fw-bold" id="aiLessonModalLabel"><i class="bi bi-stars text-warning me-2"></i> Assistente de Plano de Aula (IA)</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-dark">Qual será o tema da aula?</label>
                                            <input type="text" id="aiThemeInput" class="form-control form-control-lg bg-light border-0" placeholder="Ex: Revolução Francesa e seus impactos">
                                        </div>
                                        <div id="aiLoading" class="text-center py-4 d-none">
                                            <div class="spinner-grow text-primary mb-3" role="status"></div>
                                            <p class="text-muted fw-bold mb-0">Analisando currículo e gerando plano...</p>
                                        </div>
                                        <div id="aiResultContainer" class="d-none">
                                            <label class="form-label fw-bold text-success small text-uppercase">Plano Sugerido:</label>
                                            <div id="aiResultText" class="p-3 bg-light rounded text-dark small" style="white-space: pre-wrap;"></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-primary fw-bold" id="btnGenerateAI">
                                            <i class="bi bi-magic me-1"></i> Gerar Plano
                                        </button>
                                        <button type="button" class="btn btn-success fw-bold d-none" id="btnUsePlanAI" data-bs-dismiss="modal">
                                            <i class="bi bi-check-lg me-1"></i> Usar este Plano
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const btnGenerate = document.getElementById('btnGenerateAI');
                                const btnUse = document.getElementById('btnUsePlanAI');
                                const inputTheme = document.getElementById('aiThemeInput');
                                const loading = document.getElementById('aiLoading');
                                const resultContainer = document.getElementById('aiResultContainer');
                                const resultText = document.getElementById('aiResultText');
                                const notesInput = document.getElementById('notes');

                                let generatedPlan = '';

                                btnGenerate.addEventListener('click', function() {
                                    const theme = inputTheme.value.trim();
                                    if(!theme) {
                                        window.Toast.fire({icon: 'warning', title: 'Digite o tema da aula!'});
                                        return;
                                    }

                                    // Reset UI
                                    resultContainer.classList.add('d-none');
                                    btnUse.classList.add('d-none');
                                    btnGenerate.classList.add('d-none');
                                    loading.classList.remove('d-none');

                                    // Simula a chamada da API da IA (delay de 2s)
                                    setTimeout(() => {
                                        generatedPlan = `Tema: ${theme}\n\n🎯 Objetivo:\nCompreender os principais aspectos e impactos relacionados ao tema central, desenvolvendo senso crítico.\n\n📚 Metodologia:\nAula expositiva dialogada inicial, seguida de divisão em pequenos grupos para estudo de caso (Sala Invertida) e debate.\n\n✅ Avaliação:\nParticipação no debate e entrega de um mapa mental ao final da aula.`;
                                        
                                        resultText.textContent = generatedPlan;
                                        
                                        loading.classList.add('d-none');
                                        resultContainer.classList.remove('d-none');
                                        btnUse.classList.remove('d-none');
                                        btnGenerate.classList.remove('d-none');
                                        btnGenerate.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Gerar Novamente';
                                    }, 2000);
                                });

                                btnUse.addEventListener('click', function() {
                                    notesInput.value = generatedPlan;
                                });
                            });
                        </script>

                        <h6 class="fw-bold text-primary mb-3 mt-4"><i class="bi bi-list-check me-2"></i> Lista de Chamada</h6>
                        
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
