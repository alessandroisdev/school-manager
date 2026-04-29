<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Enturmação Inteligente (IA)</h2>
                <p class="text-muted small mb-0">Distribua alunos em lote de forma balanceada respeitando os limites das salas.</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Painel Esquerdo: Alunos Sem Turma -->
            <div class="col-lg-7">
                <div class="glass-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-people me-2"></i> Fila de Espera (Alunos sem Turma)</h5>
                        <span class="badge bg-secondary rounded-pill px-3 py-2">{{ $unenrolledStudents->count() }} Alunos</span>
                    </div>

                    <form id="smartEnrollForm" action="{{ route('academic.smart.autoEnroll') }}" method="POST">
                        @csrf
                        
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th width="40" class="text-center">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </th>
                                        <th>Nome do Aluno</th>
                                        <th>Data de Nasc.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($unenrolledStudents as $student)
                                        <tr>
                                            <td class="text-center">
                                                <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="{{ $student->id }}">
                                            </td>
                                            <td class="fw-bold">{{ $student->name }}</td>
                                            <td class="text-muted small">{{ $student->birth_date ? $student->birth_date->format('d/m/Y') : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Todos os alunos ativos já estão enturmados!</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>

            <!-- Painel Direito: Configuração da IA -->
            <div class="col-lg-5">
                <div class="glass-card p-4 h-100" style="background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);">
                    <h5 class="fw-bold text-primary mb-4"><i class="bi bi-magic me-2"></i> Assistente de Balanceamento</h5>
                    
                    <div class="alert alert-info border-0 shadow-sm small">
                        <i class="bi bi-info-circle me-1"></i> A Inteligência Artificial irá dividir os alunos selecionados matematicamente entre as turmas da Série escolhida, garantindo que as turmas fiquem com a mesma quantidade de alunos.
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">1. Selecione a Série (Grade) Destino:</label>
                        <select name="grade_id" class="form-select border-secondary py-3 fw-bold" required>
                            <option value="">-- Escolha a Série --</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark">2. Status Atual das Turmas:</label>
                        <div class="list-group list-group-flush small overflow-auto" style="max-height: 150px;">
                            @foreach($grades as $grade)
                                @foreach($grade->classes as $class)
                                    <div class="list-group-item bg-transparent px-0 py-1 d-flex justify-content-between">
                                        <span class="text-muted">{{ $grade->name }} - {{ $class->name }}</span>
                                        <span class="badge bg-light text-dark border">{{ $class->enrollments_count }} matriculados</span>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 py-3 fw-bold shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border: none; color: white;">
                        <i class="bi bi-lightning-fill me-1"></i> Executar Enturmação em Lote
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('selectAll').addEventListener('change', function(e) {
            let checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
        });
    </script>
</x-app-layout>
