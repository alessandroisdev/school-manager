<x-app-layout>
    <div class="container-fluid">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1 text-dark fw-bold">Configurações da Unidade</h2>
                <p class="text-muted small mb-0">Parâmetros globais para o funcionamento desta franquia.</p>
            </div>
        </div>

        <div class="glass-card p-4 mx-auto" style="max-width: 800px;">
            <form action="{{ route('admin.settings.store') }}" method="POST">
                @csrf
                
                <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-gear-fill me-2"></i> Regras Acadêmicas e de Lotação</h5>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">Máximo de Alunos por Turma (Default) *</label>
                        <input type="number" name="settings[default_class_capacity]" class="form-control bg-light border-0" value="{{ $settings['default_class_capacity'] ?? 30 }}" required>
                        <div class="form-text small">Limita a enturmação automática e manual.</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">Nota Média de Aprovação *</label>
                        <input type="number" step="0.1" name="settings[passing_grade]" class="form-control bg-light border-0" value="{{ $settings['passing_grade'] ?? 6.0 }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">Frequência Mínima para Aprovação (%) *</label>
                        <input type="number" name="settings[minimum_attendance_percent]" class="form-control bg-light border-0" value="{{ $settings['minimum_attendance_percent'] ?? 75 }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">Ano Letivo Corrente *</label>
                        <input type="number" name="settings[current_academic_year]" class="form-control bg-light border-0" value="{{ $settings['current_academic_year'] ?? date('Y') }}" required>
                    </div>
                </div>

                <hr class="my-4 border-secondary">
                
                <h5 class="fw-bold mb-4 text-success"><i class="bi bi-wallet2 me-2"></i> Financeiro</h5>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">Dia Padrão de Vencimento *</label>
                        <input type="number" name="settings[default_due_day]" class="form-control bg-light border-0" value="{{ $settings['default_due_day'] ?? 10 }}" min="1" max="28" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">Juros por Atraso (%) ao mês</label>
                        <input type="number" step="0.1" name="settings[late_fee_interest]" class="form-control bg-light border-0" value="{{ $settings['late_fee_interest'] ?? 2.0 }}">
                    </div>
                </div>

                <hr class="my-4 border-secondary">

                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-save me-2"></i> Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
